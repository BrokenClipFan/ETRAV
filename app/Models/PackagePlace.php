<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Package;
use App\Models\Place;

class PackagePlace extends Model
{

    protected $fillable = [
        'package_id',
        'place_id',
        'position',
    ];

    public function package() {
        return $this->belongsTo(Package::class);
    }

    public function place() {
        return $this->belongsTo(Place::class);
    }
}
