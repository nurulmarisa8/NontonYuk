<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Schedule;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    private function checkAdmin()
    {
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            abort(403, 'You must be an admin to access this area.');
        }
    }

    // Menampilkan Dashboard Admin
    public function index()
    {
        $this->checkAdmin();

        $totalMovies = Movie::count();
        $totalSchedules = Schedule::count();
        $totalBookings = Booking::count();

        return view('admin.dashboard', compact('totalMovies', 'totalSchedules', 'totalBookings'));
    }

   // Menampilkan semua film
    public function movies()
    {
        $this->checkAdmin();
        $movies = Movie::all();
        return view('admin.movies.index', compact('movies'));
    }


    // menampilkan form untuk menambahkan film baru
    public function createMovie()
    {
        $this->checkAdmin();
        return view('admin.movies.create');
    }


    public function storeMovie(Request $request)
    {
        $this->checkAdmin();
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'duration' => 'nullable|integer',
            'genre' => 'nullable|string|max:100',
            'age_rating' => 'nullable|string|max:10',
            'release_date' => 'nullable|date',
            'status' => 'nullable|in:active,inactive,coming_soon,now_showing',
            'poster' => 'nullable|url|max:2048',
        ]);

        $posterPath = null;

        if ($request->filled('poster')) {
            // Validate if it's a valid URL
            $posterInput = $request->poster;
            if (filter_var($posterInput, FILTER_VALIDATE_URL)) {
                $posterPath = $posterInput;
            }
        }

        Movie::create([
            'title' => $request->title,
            'description' => $request->description,
            'duration' => $request->duration,
            'genre' => $request->genre,
            'age_rating' => $request->age_rating,
            'release_date' => $request->release_date,
            'status' => $request->status ?? 'active',
            'poster' => $posterPath,
        ]);

        return redirect()->route('admin.movies.index')->with('success', 'Movie created successfully.');
    }

    /**
     * Show the form to edit a movie
     */
    public function editMovie($id)
    {
        $this->checkAdmin();
        $movie = Movie::findOrFail($id);
        return view('admin.movies.edit', compact('movie'));
    }

    /**
     * Update a movie
     */
    public function updateMovie(Request $request, $id)
    {
        $this->checkAdmin();
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'duration' => 'nullable|integer',
            'genre' => 'nullable|string|max:100',
            'age_rating' => 'nullable|string|max:10',
            'release_date' => 'nullable|date',
            'status' => 'nullable|in:active,inactive,coming_soon,now_showing',
            'poster' => 'nullable|url|max:2048',
        ]);

        $movie = Movie::findOrFail($id);

        $posterPath = $movie->poster; // Keep the existing poster if not updating

        if ($request->filled('poster')) {
            // Validate if it's a valid URL
            $posterInput = $request->poster;
            if (filter_var($posterInput, FILTER_VALIDATE_URL)) {
                $posterPath = $posterInput;
            }
        }

        $movie->update([
            'title' => $request->title,
            'description' => $request->description,
            'duration' => $request->duration,
            'genre' => $request->genre,
            'age_rating' => $request->age_rating,
            'release_date' => $request->release_date,
            'status' => $request->status ?? 'active',
            'poster' => $posterPath,
        ]);

        return redirect()->route('admin.movies.index')->with('success', 'Movie updated successfully.');
    }

    /**
     * Delete a movie
     */
    public function deleteMovie($id)
    {
        $this->checkAdmin();
        $movie = Movie::findOrFail($id);
        $movie->delete();

        return redirect()->route('admin.movies.index')->with('success', 'Movie deleted successfully.');
    }

    /**
     * Show all schedules
     */
    public function schedules()
    {
        $this->checkAdmin();
        $schedules = Schedule::with(['movie'])->get();
        return view('admin.schedules.index', compact('schedules'));
    }

    /**
     * Show the form to create a new schedule
     */
    public function createSchedule()
    {
        $this->checkAdmin();
        $movies = Movie::where('status', 'active')->get();
        return view('admin.schedules.create', compact('movies'));
    }

    /**
     * Store a new schedule
     */
    public function storeSchedule(Request $request)
    {
        $this->checkAdmin();
        $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'showtime' => 'required|date',
            'total_seats' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        Schedule::create([
            'movie_id' => $request->movie_id,
            'showtime' => $request->showtime,
            'total_seats' => $request->total_seats,
            'available_seats' => $request->total_seats, // Initially all seats are available
            'price' => $request->price,
        ]);

        return redirect()->route('admin.schedules.index')->with('success', 'Schedule created successfully.');
    }

    /**
     * Show the form to edit a schedule
     */
    public function editSchedule($id)
    {
        $this->checkAdmin();
        $schedule = Schedule::with(['movie'])->findOrFail($id);
        $movies = Movie::where('status', 'active')->get();
        return view('admin.schedules.edit', compact('schedule', 'movies'));
    }

    /**
     * Update a schedule
     */
    public function updateSchedule(Request $request, $id)
    {
        $this->checkAdmin();
        $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'showtime' => 'required|date',
            'total_seats' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        $schedule = Schedule::findOrFail($id);
        $schedule->update([
            'movie_id' => $request->movie_id,
            'showtime' => $request->showtime,
            'total_seats' => $request->total_seats,
            'available_seats' => $request->total_seats, // Update available seats if total seats changed
            'price' => $request->price,
        ]);

        return redirect()->route('admin.schedules.index')->with('success', 'Schedule updated successfully.');
    }

    /**
     * Delete a schedule
     */
    public function deleteSchedule($id)
    {
        $this->checkAdmin();
        $schedule = Schedule::findOrFail($id);
        $schedule->delete();

        return redirect()->route('admin.schedules.index')->with('success', 'Schedule deleted successfully.');
    }

    /**
     * Show admin login form
     */
    public function showLoginForm()
    {
        return view('admin.login');
    }

    /**
     * Handle admin login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $credentials = $request->only('email', 'password');

        // Check if user exists and is admin
        $user = \App\Models\User::where('email', $request->email)->first();

        if (!$user || !$user->isAdmin() || !Auth::attempt($credentials)) {
            return back()->withErrors([
                'email' => 'Credentials do not match an admin account.',
            ])->withInput($request->only('email'));
        }

        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    /**
     * Handle admin logout
     */
    public function logout()
    {
        $this->checkAdmin();
        Auth::logout();
        return redirect()->route('home');
    }
}
