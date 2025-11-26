<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Schedule;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        // Mengambil semua film aktif beserta jadwal tayangnya untuk user biasa
        $movies = Movie::with(['schedules' => function($query) {
                $query->where('showtime', '>=', now())
                      ->orderBy('showtime');
            }])
            ->where('status', 'active')
            ->orWhere('status', 'now_showing')
            ->get();

        // Also get coming soon movies
        $comingSoonMovies = Movie::where('status', 'coming_soon')
            ->where('release_date', '>', now())
            ->get();

        return view('user.dashboard', compact('movies', 'comingSoonMovies'));
    }

    public function showSchedule($movieId)
    {
        $movie = Movie::with(['schedules' => function($query) {
                $query->where('showtime', '>=', now())
                      ->orderBy('showtime');
            }])
            ->findOrFail($movieId);

        return view('user.schedules', compact('movie'));
    }
}