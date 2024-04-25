<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $fillable = ['user_id', 'product_id'];
    public $timestamps = false;
}
