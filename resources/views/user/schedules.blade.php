@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Jadwal Tayang - {{ $movie->title }}</h1>

    <div class="movie-info">
        <p><strong>Judul:</strong> {{ $movie->title }}</p>
        <p><strong>Deskripsi:</strong> {{ $movie->description }}</p>
        <p><strong>Durasi:</strong> {{ $movie->duration }} menit</p>
        <p><strong>Genre:</strong> {{ $movie->genre }}</p>
    </div>

    <div class="schedules mt-4">
        <h2>Jadwal Tayang</h2>
        @if($movie->schedules->count() > 0)
            <div class="row">
                @foreach($movie->schedules as $schedule)
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">{{ \Carbon\Carbon::parse($schedule->showtime)->format('d M Y') }}</h5>
                                <p class="card-text">
                                    <strong>Waktu:</strong> {{ \Carbon\Carbon::parse($schedule->showtime)->format('H:i') }}<br>
                                    <strong>Room:</strong> {{ $schedule->room ?? 'Main Theater' }}<br>
                                    <strong>Harga:</strong> Rp {{ number_format($schedule->price, 0, ',', '.') }}
                                </p>
                                <a href="{{ route('booking.create', $schedule->id) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded-lg transition duration-300 transform hover:scale-105">Pesan Tiket</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p>Tidak ada jadwal tayang tersedia saat ini.</p>
        @endif
    </div>

    <div class="mt-3">
        <a href="{{ route('user.dashboard') }}" class="btn btn-secondary">Kembali ke Dashboard</a>
    </div>
</div>
@endsection