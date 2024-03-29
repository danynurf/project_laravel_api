<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory;

    protected $table = 'products_categories';

    protected $guarded = [];

    public function category()
    {
        return $this->hasOne(Category::class);
    }
}
