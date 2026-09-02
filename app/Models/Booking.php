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
        'vehicle_id',
        'pickup_datetime',
        'latitude',
        'longitude',
        'pickup_place_name',
        'pax',
        'distance',
        'total_price',
        'head_price',
        'deposit_amount',
        'amount_paid',
        'joiners',
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

    public function vehicle()
    {
        return $this->belongsTo(Transport::class, 'vehicle_id');
    }

    public function itinerary()
    {
        return $this->hasMany(BookingPlace::class)->orderBy('id');
    }
}
