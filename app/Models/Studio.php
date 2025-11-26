<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Studio extends Model
{

    protected $fillable = [
        'name',
        'total_rows',
        'seats_per_row',
        'layout',
        'inactive_seats',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'layout' => 'array',
        'inactive_seats' => 'array',
        'total_rows' => 'integer',
        'seats_per_row' => 'integer',
    ];

    /**
     * Get the schedules for the studio.
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }
}