<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HdrOrder extends Model
{
    use HasFactory;

    protected $table = 'hdr_orders';

    protected $guarded = [];

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'dtl_orders');
    }
}
