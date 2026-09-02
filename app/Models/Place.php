<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PackagePlace;

class Place extends Model
{
    protected $fillable = [
        'name',
        'price',
        'description',
        'category',
        'place',
        'image_path',
        'latitude',
        'longitude'
    ];

    public function packages() {
        return $this->hasMany(PackagePlace::class, 'place_id');
    }

    public function bookingPlaces() {
        return $this->hasMany(BookingPlace::class, 'place_id');
    }
}
