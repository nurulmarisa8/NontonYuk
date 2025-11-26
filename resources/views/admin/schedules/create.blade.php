@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Add New Schedule</h1>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('admin.schedules.store') }}">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="movie_id" class="block text-sm font-medium text-gray-700 mb-1">Movie *</label>
                    <select id="movie_id" name="movie_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Select a movie</option>
                        @foreach($movies as $movie)
                            <option value="{{ $movie->id }}" {{ old('movie_id') == $movie->id ? 'selected' : '' }}>
                                {{ $movie->title }}
                            </option>
                        @endforeach
                    </select>
                    @error('movie_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>


                <div>
                    <label for="showtime" class="block text-sm font-medium text-gray-700 mb-1">Showtime *</label>
                    <input type="datetime-local" id="showtime" name="showtime" value="{{ old('showtime') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @error('showtime')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="total_seats" class="block text-sm font-medium text-gray-700 mb-1">Total Seats *</label>
                    <select id="total_seats" name="total_seats" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Select total seats</option>
                        <option value="15" {{ old('total_seats') == 15 ? 'selected' : '' }}>15 Seats</option>
                        <option value="30" {{ old('total_seats') == 30 ? 'selected' : '' }}>30 Seats</option>
                        <option value="50" {{ old('total_seats') == 50 ? 'selected' : '' }}>50 Seats</option>
                        <option value="100" {{ old('total_seats') == 100 ? 'selected' : '' }}>100 Seats</option>
                    </select>
                    @error('total_seats')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Price (Rp) *</label>
                    <input type="number" id="price" name="price" value="{{ old('price', 0) }}" min="0" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @error('price')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('admin.schedules.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Create Schedule</button>
            </div>
        </form>
    </div>
</div>
@endsection

