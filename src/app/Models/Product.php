<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $guarded = ['id'];
    public function getRouteKeyName():string
    {
        return 'code';
    }
    public function category():BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_slug', 'slug');
    }
}
