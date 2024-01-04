<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductModel extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "slug",
        "selling_price",
        "original_price",
        "qty",
        "image",
        "featured",
        "category_id",
        "status",
        "popular",
        "description",
    ];

    public function CartModel()
    {
        return $this->hasMany(CartModel::class);
    }


    protected  $with = ['category_models'];

    public function category_models()
    {
        return $this->belongsTo(CategoryModel::class, 'category_id', 'id');
    }
}
