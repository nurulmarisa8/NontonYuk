@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Admin Dashboard</h1>
        <p class="text-gray-600 mt-2">Manage your cinema's movies, schedules, and bookings</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 mr-4">
                    <i class="fas fa-film text-blue-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-500">Movies</p>
                    <p class="text-2xl font-bold">{{ $totalMovies }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 mr-4">
                    <i class="fas fa-calendar text-green-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-500">Schedules</p>
                    <p class="text-2xl font-bold">{{ $totalSchedules }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 mr-4">
                    <i class="fas fa-ticket-alt text-yellow-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-500">Bookings</p>
                    <p class="text-2xl font-bold">{{ $totalBookings }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('admin.movies.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white py-3 px-4 rounded-lg text-center transition flex items-center justify-center">
                <i class="fas fa-plus-circle mr-2"></i> Add New Movie
            </a>
            <a href="{{ route('admin.schedules.create') }}" class="bg-green-600 hover:bg-green-700 text-white py-3 px-4 rounded-lg text-center transition flex items-center justify-center">
                <i class="fas fa-plus-circle mr-2"></i> Add New Schedule
            </a>
            <a href="{{ route('admin.movies.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg text-center transition flex items-center justify-center">
                <i class="fas fa-list mr-2"></i> View All Movies
            </a>
        </div>
    </div>

</div>
@endsection