<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Translation extends Model
{
    protected $fillable = [
        'language',
        'code',
        'locale_code',
    ];
}
