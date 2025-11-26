<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schedule extends Model
{
    /**
     * Atribut yang dapat diisi massal.
     *
     * @var array
     */
    protected $fillable = [
        'movie_id',        
        'showtime',        
        'total_seats',     
        'available_seats', 
        'price',           
    ];

 
    protected $casts = [
        'showtime' => 'datetime',    
        'price' => 'decimal:2',      
    ];


    public function movie(): BelongsTo
    {
        return $this->belongsTo(Movie::class);
    }


    public function studio(): BelongsTo
    {
        return $this->belongsTo(Studio::class);
    }


    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
