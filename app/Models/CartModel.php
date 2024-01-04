<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Workbench\App\Models\User;

class CartModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
    ];


    protected $with = ['product_models'];
    public function product_models()
    {
        return $this->belongsTo(ProductModel::class, 'product_id', 'id');
    }
}
