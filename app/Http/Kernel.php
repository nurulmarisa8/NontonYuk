<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * Tumpukan middleware HTTP global aplikasi.
     *
     * Middleware ini dijalankan selama setiap permintaan ke aplikasi Anda.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,        // Middleware untuk menangani proxy terpercaya
        \Illuminate\Http\Middleware\HandleCors::class,   // Middleware untuk menangani Cross-Origin Resource Sharing
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class, // Middleware untuk mencegah permintaan saat maintenance
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class, // Middleware untuk memvalidasi ukuran POST
        \App\Http\Middleware\TrimStrings::class,        // Middleware untuk memotong spasi pada string
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class, // Middleware untuk mengkonversi string kosong ke null
    ];

    /**
     * Grup middleware route aplikasi.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,            // Middleware untuk mengenkripsi cookies
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class, // Middleware untuk menambahkan cookies ke response
            \Illuminate\Session\Middleware\StartSession::class,    // Middleware untuk memulai sesi
            \Illuminate\View\Middleware\ShareErrorsFromSession::class, // Middleware untuk berbagi error dari sesi
            \App\Http\Middleware\VerifyCsrfToken::class,           // Middleware untuk verifikasi token CSRF
            \Illuminate\Routing\Middleware\SubstituteBindings::class, // Middleware untuk substitusi binding
        ],

        'api' => [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api', // Middleware untuk membatasi permintaan API
            \Illuminate\Routing\Middleware\SubstituteBindings::class, // Middleware untuk substitusi binding
        ],
    ];

    /**
     * Alias middleware aplikasi.
     *
     * Alias dapat digunakan sebagai pengganti nama kelas untuk menetapkan middleware ke route dan grup dengan mudah.
     *
     * @var array<string, class-string|string>
     */
    protected $middlewareAliases = [
        'auth' => \App\Http\Middleware\Authenticate::class,            // Middleware untuk autentikasi
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class, // Middleware untuk autentikasi dasar
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class, // Middleware untuk autentikasi sesi
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class, // Middleware untuk header cache
        'can' => \Illuminate\Auth\Middleware\Authorize::class,         // Middleware untuk otorisasi
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class, // Middleware untuk mengarahkan jika sudah login
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class, // Middleware untuk konfirmasi password
        'precognitive' => \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class, // Middleware untuk permintaan precognitive
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class, // Middleware untuk validasi signature
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class, // Middleware untuk pembatasan permintaan
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class, // Middleware untuk verifikasi email
    ];
}