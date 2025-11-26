@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Manage Movies</h1>
        <a href="{{ route('admin.movies.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded-lg flex items-center">
            <i class="fas fa-plus-circle mr-2"></i> Add Movie
        </a>
    </div>

    @if($movies->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($movies as $movie)
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    @if($movie->poster_url)
                        <img src="{{ $movie->poster_url }}" alt="{{ $movie->title }}" class="w-full h-64 object-cover aspect-[2/3]">
                    @else
                        <div class="w-full h-64 bg-gray-200 flex items-center justify-center aspect-[2/3]">
                            <span class="text-gray-500">No poster</span>
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
                        <p class="text-sm text-gray-600">
                            <span class="font-semibold">Status:</span>
                            <span class="px-2 py-1 rounded text-xs {{ $movie->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($movie->status) }}
                            </span>
                        </p>
                        <p class="text-gray-700 mt-2 text-sm">{{ \Illuminate\Support\Str::limit($movie->description, 100) }}</p>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 flex justify-end space-x-2">
                        <a href="{{ route('admin.movies.edit', $movie->id) }}" class="text-indigo-600 hover:text-indigo-900">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form method="POST" action="{{ route('admin.movies.delete', $movie->id) }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this movie?')">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <i class="fas fa-film text-5xl text-gray-400 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-600">No movies found</h3>
            <p class="text-gray-500 mt-2">Get started by adding a new movie.</p>
            <a href="{{ route('admin.movies.create') }}" class="mt-4 inline-block bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded-lg">
                Add Your First Movie
            </a>
        </div>
    @endif
</div>
@endsection