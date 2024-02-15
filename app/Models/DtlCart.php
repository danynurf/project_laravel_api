<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DtlCart extends Model
{
    use HasFactory;

    protected $table = 'dtl_carts';

    protected $guarded = [];

    public function product()
    {
        return $this->hasOne(Product::class, 'id');
    }
}
