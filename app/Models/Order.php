<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $fillable = ['user_id', 'recipient_name', 'cart_code', 'province', 'district', 'ward', 'address', 'phone', 'total_all', 'payment_method', 'order_status', 'payment_status'];
}
