<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderModel extends Model
{
    use HasFactory;

    protected $table = 'order_items';
    protected $fillable = [
        'user_id',
        'product_id',
        'status',
    ];
}
