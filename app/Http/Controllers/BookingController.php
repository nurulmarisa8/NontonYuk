<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class BookingController extends Controller
{
    // Menampilkan Halaman Pilih Kursi
    public function create($scheduleId)
    {
        // Mengambil data jadwal dengan informasi film terkait
        $schedule = Schedule::with(['movie'])->findOrFail($scheduleId);

        // Ambil semua kursi yang SUDAH terisi untuk jadwal ini
        $bookedSeats = Booking::where('schedule_id', $scheduleId)
                              ->where('status', 'paid')
                              ->pluck('seat_number')
                              ->toArray();

        // Ambil semua kursi yang sedang di-lock (masih dalam masa lock)
        $lockedSeats = Booking::where('schedule_id', $scheduleId)
                              ->where('status', 'locked')
                              ->where('locked_until', '>', Carbon::now())
                              ->pluck('seat_number')
                              ->toArray();

        // Gabungkan kursi terisi dan terkunci
        $unavailableSeats = array_merge($bookedSeats, $lockedSeats);


        return view('booking.create', compact('schedule', 'bookedSeats', 'unavailableSeats'));
    }

    // Proses Ambil Kursi (Lock) - untuk multiple seat selection
    public function lockSeats(Request $request, $scheduleId)
    {
        // Validasi inputan kursi yang dipilih
        $request->validate([
            'seats' => 'required|array|min:1',
            'seats.*' => 'required|string',
        ]);

        // Cari jadwal yang dipilih
        $schedule = Schedule::findOrFail($scheduleId);

        // Validasi apakah kursi yang dipilih melebihi kapasitas
        if (count($request->seats) > $schedule->available_seats) {
            return response()->json([
                'success' => false,
                'message' => 'Jumlah kursi yang dipilih melebihi kapasitas tersedia.'
            ], 400);
        }

        // Ambil kursi yang akan di-lock
        $seatsToLock = $request->seats;

        try {
            DB::beginTransaction();

            // Lepaskan lock kursi yang sudah kadaluarsa untuk user ini pada jadwal ini
            $releasedCount = Booking::where('schedule_id', $scheduleId)
                                   ->where('status', 'locked')
                                   ->where('locked_until', '<=', Carbon::now())
                                   ->update(['status' => 'cancelled']);

            // Cek ketersediaan kursi yang diminta menggunakan FOR UPDATE untuk mencegah kondisi race
            $existingBookings = Booking::where('schedule_id', $scheduleId)
                                       ->where(function($query) {
                                           $query->where('status', 'paid')
                                                 ->orWhere(function($subquery) {
                                                     $subquery->where('status', 'locked')
                                                              ->where('locked_until', '>', Carbon::now());
                                                 });
                                       })
                                       ->whereIn('seat_number', $seatsToLock)
                                       ->lockForUpdate()
                                       ->get();

            // Ambil daftar kursi yang tidak tersedia
            $unavailableSeats = $existingBookings->pluck('seat_number')->toArray();

            // Jika ada kursi yang tidak tersedia, batalkan transaksi
            if (!empty($unavailableSeats)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Beberapa kursi yang dipilih sudah tidak tersedia: ' . implode(', ', $unavailableSeats)
                ], 400);
            }

            // Lock kursi yang dipilih
            foreach ($seatsToLock as $seatNumber) {
                $booking = new Booking();
                $booking->user_id = Auth::id();
                $booking->schedule_id = $scheduleId;
                $booking->movie_id = $schedule->movie_id;
                $booking->seat_number = $seatNumber;
                $booking->customer_name = Auth::user() ? Auth::user()->name : 'Guest';
                $booking->customer_phone = null; // Akan diisi saat booking disimpan dengan input customer
                $booking->booking_date = Carbon::now(); // Set tanggal booking
                $booking->status = 'locked';
                $booking->locked_until = Carbon::now()->addMinutes(5); // Lock selama 5 menit
                $booking->save();
            }

            DB::commit();

            // Kembalikan respons bahwa kursi berhasil di-lock
            return response()->json([
                'success' => true,
                'message' => 'Kursi berhasil di-lock selama 5 menit',
                'locked_seats' => $seatsToLock
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal meng-lock kursi: ' . $e->getMessage()
            ], 500);
        }
    }

    // Proses Simpan Pemesanan - setelah pembayaran
    public function store(Request $request)
    {
        // Validasi input data booking
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'seats' => 'required|array|min:1',
            'seats.*' => 'required|string',
            'customer_name' => 'required|string',
            'customer_phone' => 'required|string',
            'payment_method' => 'sometimes|string', // Diubah dari required menjadi sometimes
        ]);

        // Ambil data jadwal terkait
        $schedule = Schedule::findOrFail($request->schedule_id);

        // Set metode pembayaran ke dummy jika tidak disediakan (untuk booking yang lancar)
        $paymentMethod = $request->payment_method ?? 'dummy';

        // Cek apakah metode pembayaran valid (untuk sekarang, hanya cek apakah disediakan)
        if ($paymentMethod !== 'dummy') {
            return back()->with('error', 'Metode pembayaran tidak valid.');
        }

        try {
            DB::beginTransaction();

            // Ambil kursi yang di-lock oleh user ini yang belum kadaluarsa
            $lockedBookings = Booking::where('schedule_id', $request->schedule_id)
                                     ->where('user_id', Auth::id())
                                     ->where('status', 'locked')
                                     ->where('locked_until', '>', Carbon::now())
                                     ->whereIn('seat_number', $request->seats)
                                     ->get();

            // Cek bahwa semua kursi yang diminta saat ini di-lock oleh user ini
            $lockedSeatNumbers = $lockedBookings->pluck('seat_number')->toArray();
            $unlockedSeats = array_diff($request->seats, $lockedSeatNumbers);

            // Jika ada kursi yang tidak di-lock, kembalikan error
            if (!empty($unlockedSeats)) {
                return back()->with('error', 'Beberapa kursi yang dipilih sudah tidak tersedia: ' . implode(', ', $unlockedSeats));
            }

            // Update booking yang di-lock ke status paid dan set ID tiket
            $totalPrice = 0;
            foreach ($lockedBookings as $booking) {
                $totalPrice += $schedule->price;
                $booking->status = 'paid';
                $booking->customer_name = $request->customer_name;
                $booking->customer_phone = $request->customer_phone;
                $booking->total_price = $schedule->price;
                $booking->locked_until = null;
                $booking->save();
            }

            // Update jumlah kursi tersedia di jadwal
            $schedule->available_seats = max(0, $schedule->available_seats - count($lockedBookings));
            $schedule->save();

            DB::commit();

            // Kirim notifikasi WhatsApp menggunakan API Fonnte jika dikonfigurasi
            if (env('FONNTE_TOKEN')) {
                $this->sendWhatsAppNotification($lockedBookings, $request->customer_name, $request->customer_phone, $schedule, $totalPrice);
            }

            // Redirect ke halaman tiket saya
            return redirect()->route('my.tickets')->with('success', 'Tiket berhasil dibooking!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    // Format pesan kursi untuk WhatsApp
    private function formatSeatsMessage($bookings)
    {
        $seatNumbers = $bookings->pluck('seat_number')->toArray();
        return "💺 Kursi: " . implode(', ', $seatNumbers) . "\n";
    }

    // Format nomor telepon untuk WhatsApp (konversi 08xxx ke 628xxx)
    private function formatWhatsAppNumber($phone)
    {
        // Hapus karakter non-numerik
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Konversi dari format Indonesia (08xxx) ke format internasional (628xxx)
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        } elseif (substr($phone, 0, 2) === '62') {
            // Sudah dalam format internasional
        } else {
            // Tambahkan kode negara jika diperlukan, default ke Indonesia
            $phone = '62' . $phone;
        }

        return $phone;
    }

    // Lepaskan lock kursi jika pembayaran tidak selesai
    public function releaseLock($scheduleId)
    {
        // Periksa apakah user sudah login
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'User not authenticated']);
        }

        // Update status booking yang di-lock menjadi cancelled
        $releasedCount = Booking::where('schedule_id', $scheduleId)
                                ->where('user_id', Auth::id())
                                ->where('status', 'locked')
                                ->where('locked_until', '>', Carbon::now())
                                ->update(['status' => 'cancelled']);

        // Kembalikan respons jumlah kursi yang locknya dilepas
        return response()->json([
            'success' => true,
            'message' => 'Lock berhasil dilepas',
            'released_count' => $releasedCount
        ]);
    }

    // Tampilkan tiket milik user
    public function myTickets()
    {
        // Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk melihat tiket Anda.');
        }

        // Ambil semua booking yang sudah dibayar milik user ini
        $bookings = Booking::with(['movie', 'schedule'])
                          ->where('user_id', Auth::id())
                          ->where('status', 'paid')
                          ->orderBy('created_at', 'desc')
                          ->get();

        return view('booking.my-tickets', compact('bookings'));
    }

    // Fungsi Khusus untuk Mengirim WA Fonnte
    private function sendWhatsAppNotification($bookings, $customerName, $customerPhone, $schedule, $totalPrice)
    {
        $token = env('FONNTE_TOKEN'); // Ambil dari .env

        // Jika token tidak ada, hentikan eksekusi
        if (!$token) {
            \Log::error('FONNTE_TOKEN not found in environment variables');
            return;
        }

        // Format Pesan E-Ticket
        $seatNumbers = $bookings->pluck('seat_number')->toArray();
        $message = "*E-TICKET BIOSKOP XX*\n\n" .
                   "Halo, *{$customerName}*! Terima kasih telah memesan.\n\n" .
                   "🎬 Film: *{$schedule->movie->title}*\n" .
                   "📅 Tanggal & Jam: *{$schedule->showtime->format('d M Y H:i')}*\n" .
                   "💺 Kursi: *".implode(', ', $seatNumbers)."*\n" .
                   "💰 Total: *Rp " . number_format($totalPrice, 0, ',', '.') . "*\n" .
                   "🆔 Kode Booking: #" . $bookings->first()->id . "\n\n" .
                   "Silakan tunjukkan pesan ini di loket masuk. Selamat menonton!";

        // Kirim request ke Fonnte
        try {
            $response = \Http::withHeaders([
                'Authorization' => $token,
            ])->post('https://api.fonnte.com/send', [
                'target' => $this->formatWhatsAppNumber($customerPhone), // Nomor tujuan
                'message' => $message,
                'countryCode' => '62', // Opsional, default auto convert 08 to 62
            ]);

            // Log response untuk debugging
            \Log::info('WhatsApp notification sent', [
                'booking_ids' => $bookings->pluck('id')->toArray(),
                'response' => $response->json(),
                'status' => $response->status()
            ]);

        } catch (\Exception $e) {
            // Tangani jika gagal kirim (misalnya koneksi error), agar tidak merusak flow user
            \Log::error("Gagal kirim WA: " . $e->getMessage(), [
                'booking_ids' => $bookings->pluck('id')->toArray(),
                'customer_phone' => $customerPhone,
                'error' => $e
            ]);
        }
    }
}