@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">My Tickets</h1>

    @if($bookings->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($bookings as $booking)
                <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
                    <div class="bg-indigo-700 text-white p-4">
                        <h3 class="text-lg font-bold">{{ $booking->movie->title }}</h3>
                        <p class="text-sm">{{ $booking->schedule->showtime->format('M d, Y g:i A') }}</p>
                    </div>

                    <div class="p-4">
                        <div class="mb-3">
                            <p class="text-sm text-gray-600"><span class="font-semibold">Room:</span> {{ $booking->schedule->room ?? 'Main Theater' }}</p>
                            <p class="text-sm text-gray-600"><span class="font-semibold">Seat:</span> {{ $booking->seat_number }}</p>
                            <p class="text-sm text-gray-600"><span class="font-semibold">Customer:</span> {{ $booking->customer_name }}</p>
                            <p class="text-sm text-gray-600"><span class="font-semibold">Phone:</span> {{ $booking->customer_phone }}</p>
                        </div>

                        <div class="border-t border-gray-200 pt-3">
                            <p class="text-sm font-semibold text-gray-800">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 text-center">
                        <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <i class="fas fa-ticket-alt text-5xl text-gray-400 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-600">No tickets found</h3>
            <p class="text-gray-500 mt-2">You don't have any booked tickets yet.</p>
            <a href="{{ route('home') }}" class="mt-4 inline-block bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded">
                Browse Movies
            </a>
        </div>
    @endif
</div>
@endsection