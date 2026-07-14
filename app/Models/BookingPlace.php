<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class BookingPlace extends Pivot
{
    // 1. Tell Laravel to treat this as an eloquent pivot model
    protected $table = 'booking_places';

    // 2. Define fillable properties for mass assignment
    protected $fillable = [
        'booking_id',
        'place_id',
        'duration_minutes',
    ];
}
