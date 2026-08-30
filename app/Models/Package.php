<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PackagePlace;

class Package extends Model
{
    protected $fillable = [
        'name',
        'type',
        'package_price',
        'image_path',
        'description'
    ];

    public function places()
    {
        return $this->belongsToMany(Place::class, 'package_places')
                    ->withPivot('position')
                    ->withTimestamps();
    }
    
}
