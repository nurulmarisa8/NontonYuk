<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

// Redirect root to login
Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('user.dashboard'); // Redirect to user dashboard
        }
    }
    return redirect()->route('login');
})->name('home');

// User dashboard routes
Route::get('/user/dashboard', [UserController::class, 'index'])->name('user.dashboard')->middleware('auth');

Route::get('/booking/{schedule_id}', [BookingController::class, 'create'])->name('booking.create')->middleware('auth');
Route::post('/booking', [BookingController::class, 'store'])->name('booking.store')->middleware('auth');

// Booking related routes for users
Route::post('/booking/{schedule_id}/lock-seats', [BookingController::class, 'lockSeats'])->name('booking.lock.seats')->middleware('auth');
Route::post('/booking/{schedule_id}/release-lock', [BookingController::class, 'releaseLock'])->name('booking.release.lock')->middleware('auth');

// My Tickets route
Route::get('/my-tickets', [BookingController::class, 'myTickets'])->name('my.tickets')->middleware('auth');

// Authentication routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin auth routes (for login)
Route::get('admin/login', [AdminController::class, 'showLoginForm'])->name('admin.login');
Route::post('admin/login', [AdminController::class, 'login'])->name('admin.login.submit');

// Admin routes - authentication required, authorization handled in controller
Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');

    // Movie routes
    Route::get('/movies', [AdminController::class, 'movies'])->name('admin.movies.index');
    Route::get('/movies/create', [AdminController::class, 'createMovie'])->name('admin.movies.create');
    Route::post('/movies', [AdminController::class, 'storeMovie'])->name('admin.movies.store');
    Route::get('/movies/{id}/edit', [AdminController::class, 'editMovie'])->name('admin.movies.edit');
    Route::put('/movies/{id}', [AdminController::class, 'updateMovie'])->name('admin.movies.update');
    Route::delete('/movies/{id}', [AdminController::class, 'deleteMovie'])->name('admin.movies.delete');

    // Schedule routes
    Route::get('/schedules', [AdminController::class, 'schedules'])->name('admin.schedules.index');
    Route::get('/schedules/create', [AdminController::class, 'createSchedule'])->name('admin.schedules.create');
    Route::post('/schedules', [AdminController::class, 'storeSchedule'])->name('admin.schedules.store');
    Route::get('/schedules/{id}/edit', [AdminController::class, 'editSchedule'])->name('admin.schedules.edit');
    Route::put('/schedules/{id}', [AdminController::class, 'updateSchedule'])->name('admin.schedules.update');
    Route::delete('/schedules/{id}', [AdminController::class, 'deleteSchedule'])->name('admin.schedules.delete');

    Route::post('/logout', [AdminController::class, 'logout'])->name('admin.logout');
});

