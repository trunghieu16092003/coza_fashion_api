<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $fillable = ['product_id', 'cart_code', 'product_name', 'size_id', 'color_id', 'price', 'quantity', 'total_all'];
    public $timestamps = false;
}
