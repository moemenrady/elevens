<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;



class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'bg_color',
        'text_color',
        'accent_color',
        'badge',
        'cover_image',
        'icon',
        'sort_order',
        'is_active'
    ];

    public function products()
    {
        return $this->hasMany(Product::class)
            ->where('is_available', true)
            ->orderBy('sort_order');
    }
}
