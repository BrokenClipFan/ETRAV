<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transport extends Model
{
    protected $fillable = [
        'brand',
        'model',
        'plate_number',
        'capacity',
        'base_price',
        'status',
        'front_image_path',
        'side_image_path',
        'plate_image_path',
    ];
}
