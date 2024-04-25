<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    use HasFactory;
    protected $primaryKey = 'pid';
    protected $fillable = ['cat_id', 'p_name', 'p_desc', 'p_price', 'discount', 'slug', 'rating', 'is_disabled'];
    public $timestamps = false;

    public function category()
    {
        return $this->belongsTo(Category::class, 'cat_id', 'cat_id');
    }

    public function inventories()
    {
        return $this->hasMany(ProductInventory::class, 'product_id');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id');
    }
}
