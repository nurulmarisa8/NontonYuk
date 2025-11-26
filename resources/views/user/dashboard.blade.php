@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-2">Welcome, {{ Auth::user()->name }}!</h1>
    <p class="text-gray-600 mb-8">Discover and book your favorite movies</p>

    <!-- Now Showing Section -->
    <div class="mb-12">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Now Showing</h2>
        
        @if($movies->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($movies as $movie)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        @if($movie->poster_url)
                            <img src="{{ $movie->poster_url }}" alt="{{ $movie->title }}" class="w-full h-80 object-cover aspect-[2/3]">
                        @else
                            <div class="w-full h-80 bg-gray-200 flex items-center justify-center aspect-[2/3]">
                                <span class="text-gray-500">No poster available</span>
                            </div>
                        @endif
                        <div class="p-4">
                            <h3 class="text-lg font-bold text-gray-800">{{ $movie->title }}</h3>
                            <p class="text-sm text-gray-600 mt-1">
                                <span class="font-semibold">Duration:</span> {{ $movie->duration }} mins
                            </p>
                            <p class="text-sm text-gray-600">
                                <span class="font-semibold">Genre:</span> {{ $movie->genre }}
                            </p>
                            <p class="text-sm text-gray-600">
                                <span class="font-semibold">Age Rating:</span> {{ $movie->age_rating }}
                            </p>
                            <p class="text-gray-700 mt-2 text-sm">{{ \Illuminate\Support\Str::limit($movie->description, 100) }}</p>

                            @if($movie->schedules->count() > 0)
                                <div class="mt-4">
                                    <h4 class="font-semibold text-gray-700 mb-2">Available Schedules:</h4>
                                    <ul class="space-y-2">
                                        @foreach($movie->schedules->take(3) as $schedule)
                                            <li class="text-sm flex items-center justify-between">
                                                <span class="text-gray-600">{{ $schedule->showtime->format('M d, Y g:i A') }}</span>
                                                <a href="{{ route('booking.create', $schedule->id) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded-lg transition duration-300 transform hover:scale-105">
                                                    Book Now
                                                </a>
                                            </li>
                                        @endforeach

                                        @if($movie->schedules->count() > 3)
                                            <li class="text-sm text-indigo-600 mt-3 text-center">
                                                <a href="#" class="inline-block bg-gray-200 hover:bg-gray-300 text-gray-800 py-1 px-3 rounded">
                                                    View all {{ $movie->schedules->count() }} schedules
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            @else
                                <div class="mt-4 text-sm text-gray-600">
                                    No schedules available yet
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <i class="fas fa-film text-5xl text-gray-400 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-600">No movies showing currently</h3>
                <p class="text-gray-500 mt-2">Check back later for new releases.</p>
            </div>
        @endif
    </div>

    <!-- Coming Soon Section -->
    @if($comingSoonMovies->count() > 0)
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Coming Soon</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($comingSoonMovies as $movie)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        @if($movie->poster_url)
                            <img src="{{ $movie->poster_url }}" alt="{{ $movie->title }}" class="w-full h-80 object-cover aspect-[2/3]">
                        @else
                            <div class="w-full h-80 bg-gray-200 flex items-center justify-center aspect-[2/3]">
                                <span class="text-gray-500">No poster available</span>
                            </div>
                        @endif
                        <div class="p-4">
                            <h3 class="text-lg font-bold text-gray-800">{{ $movie->title }}</h3>
                            <p class="text-sm text-gray-600 mt-1">
                                <span class="font-semibold">Release Date:</span> {{ $movie->release_date ? $movie->release_date->format('M d, Y') : 'TBA' }}
                            </p>
                            <p class="text-sm text-gray-600">
                                <span class="font-semibold">Genre:</span> {{ $movie->genre }}
                            </p>
                            <p class="text-gray-700 mt-2 text-sm">{{ \Illuminate\Support\Str::limit($movie->description, 100) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection