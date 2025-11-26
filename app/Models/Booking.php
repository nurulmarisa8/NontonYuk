<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Booking extends Model
{

    protected $fillable = [
        'ticket_id',        // ID tiket unik
        'user_id',          // ID user yang membuat booking
        'schedule_id',      // ID jadwal film
        'movie_id',         // ID film
        'seat_number',      // Nomor kursi
        'seat_numbers',     // Array nomor kursi (jika multiple)
        'customer_name',    // Nama customer
        'customer_phone',   // Nomor telepon customer
        'booking_date',     // Tanggal booking
        'total_price',      // Harga total
        'status',           // Status booking (paid, locked, cancelled)
        'locked_until',     // Waktu lock berakhir
    ];


    protected $casts = [
        'seat_numbers' => 'array', // Simpan nomor kursi sebagai array
        'locked_until' => 'datetime', // Waktu lock dalam format datetime
        'total_price' => 'decimal:2', // Harga dalam format decimal dengan 2 angka di belakang koma
    ];


    protected static function boot()
    {
        parent::boot();

        // Saat membuat booking baru, generate ID tiket jika belum ada
        static::creating(function ($model) {
            if (empty($model->ticket_id)) {
                $model->ticket_id = static::generateUniqueTicketId();
            }
        });
    }


    protected static function generateUniqueTicketId()
    {
        // Buat ID tiket dengan format TICK-XXXXX
        $ticketId = 'TICK-' . strtoupper(uniqid());

        // Ulang jika ID sudah ada di database
        while (self::where('ticket_id', $ticketId)->exists()) {
            $ticketId = 'TICK-' . strtoupper(uniqid());
        }
        return $ticketId;
    }


    public function isActiveBooking(): bool
    {
        // Jika statusnya dibayar, maka aktif
        if ($this->status === 'paid') {
            return true;
        }

        // Jika statusnya dikunci dan waktu lock belum habis, maka aktif
        if ($this->status === 'locked' && $this->locked_until && $this->locked_until->isFuture()) {
            return true;
        }

        return false;
    }


    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->where('status', 'paid') // Booking yang dibayar
              ->orWhere(function ($subQuery) {
                  $subQuery->where('status', 'locked') // Atau booking yang dikunci
                           ->where('locked_until', '>', Carbon::now()); // Dan belum habis waktunya
              });
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }


    public function movie(): BelongsTo
    {
        return $this->belongsTo(Movie::class);
    }
}
