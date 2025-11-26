<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Movie extends Model
{

    protected $fillable = [
        'title',           // Judul film
        'description',     // Deskripsi film
        'duration',        // Durasi film dalam menit
        'genre',           // Genre film
        'age_rating',      // Rating usia (misal: R, PG, dll)
        'release_date',    // Tanggal rilis
        'status',          // Status film (aktif/tidak)
        'poster',          // URL poster film
    ];


    protected $casts = [
        'release_date' => 'date',  // Tanggal rilis dalam format tanggal
    ];

    /**
     * Dapatkan URL penuh poster
     */
    public function getPosterUrlAttribute()
    {
        // Jika poster kosong atau null, kembalikan placeholder default
        if (is_null($this->poster) || empty($this->poster)) {
            // Kembalikan placeholder default - bisa berupa gambar base64 atau aset default
            return 'https://via.placeholder.com/400x600/cccccc/666666?text=No+Poster';
        }

        // Jika ini sudah berupa URL (dimulai dengan http), kembalikan apa adanya
        if (filter_var($this->poster, FILTER_VALIDATE_URL)) {
            return $this->poster;
        }

        // Jika bukan URL valid, kembalikan nilai aslinya (meskipun seharusnya URL karena validasi)
        return $this->poster;
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }


    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
