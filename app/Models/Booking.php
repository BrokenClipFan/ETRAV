<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Place;
use App\Models\BookingPlace;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'package_id',
        'pickup_datetime',
        'latitude',
        'longitude',
        'pax',
        'total_price',
        'deposit_amount',
        'status',
        'notify',
    ];

    public function places(){
        return $this->belongsToMany(Place::class, 'booking_places')
                    ->using(BookingPlace::class)
                    ->withPivot('duration_minutes')
                    ->withTimestamps();
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
