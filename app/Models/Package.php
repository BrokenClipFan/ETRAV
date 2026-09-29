<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PackagePlace;

class Package extends Model
{
    protected $fillable = [
        'name',
        'type',
        'image_path',
        'description',
        'package_price'
    ];

    public function places()
    {
        return $this->belongsToMany(Place::class, 'package_places')
                    ->withPivot('position', 'duration')
                    ->withTimestamps();
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
    
    public function getTotalDurationAttribute()
    {
        $totalMinutes = 0;
        foreach ($this->places as $place) {
            if ($place->pivot && $place->pivot->duration) {
                $parts = explode(':', $place->pivot->duration);
                if (count($parts) >= 2) {
                    $totalMinutes += ((int)$parts[0] * 60) + (int)$parts[1];
                }
            }
        }

        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;
        
        $durationText = '';
        if ($hours > 0) $durationText .= $hours . 'h ';
        if ($minutes > 0) $durationText .= $minutes . 'm';
        
        return trim($durationText) ?: '0h';
    }
}
