<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Submission extends Model
{
    use Sluggable;

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    protected $fillable = [
        'title',
        'description',
        'details',
        'image',
        'slug',
        'file_size',
        'license',
        'developer',
        'url',
        'buy_url',
        'type',
        'counter',
        'category',
        'platform'
    ];
}
