<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'name',
        'type',
        'package_price',
        'perhead_price',
        'pax',
        'image_path',
        'description'
    ];
}
