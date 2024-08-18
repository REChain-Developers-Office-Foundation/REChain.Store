<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Comment extends Model
{
    protected $fillable = [
        'title',
        'name',
        'email',
        'comment',
        'approval',
        'ip'
    ];
}
