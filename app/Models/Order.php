<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $table = 'orders';
    protected $fillable = [
        'user_id',
        'firstname',
        'lastname',
        'email',
        'address',
        'city',
        'country',
        'companyname',
        'payment_id',
        'city',
        'tracking_no',
        'status',
        'remark',
        'zip'
    ];


    public function order_items()
    {
        return $this->hasMany(OrderItemModel::class, 'order_id', 'id');
    }
}
