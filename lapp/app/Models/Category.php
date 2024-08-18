<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Category extends Model
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
        'fa_icon',
        'slug',
        'application_category',
        'navbar',
        'footer'
    ];

    public function applications()
{
     return $this->belongsToMany(Application::class);
}

public function news()
{
     return $this->belongsToMany(News::class);
}
    
    
}
