<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DtlOrder extends Model
{
    use HasFactory;

    protected $table = 'dtl_orders';

    protected $guarded = [];
}
