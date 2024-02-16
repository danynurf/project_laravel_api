<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class HdrOrder extends Model
{
    use HasFactory;

    protected $table = 'hdr_orders';

    protected $guarded = [];

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'dtl_orders');
    }
}
