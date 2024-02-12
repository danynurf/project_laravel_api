<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DtlCart extends Model
{
    use HasFactory;

    protected $table = 'dtl_carts';

    protected $guarded = [];
}
