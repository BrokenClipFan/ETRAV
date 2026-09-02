<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class BookingPlace extends Pivot
{
    // 1. Tell Laravel to treat this as an eloquent pivot model
    protected $table = 'booking_places';

    protected $fillable = [
        'booking_id',
        'place_id',
        'custom_name',
        'custom_latitude',
        'custom_longitude',
        'custom_category',
        'duration_minutes',
    ];

    public function place()
    {
        return $this->belongsTo(Place::class, 'place_id');
    }
}
