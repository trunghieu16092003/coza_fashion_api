<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function add(Request $request)
    {
        $userId = auth()->user()->id;
        $productId = $request->input('product_id');

        $existingWishlist = Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($existingWishlist) {
            return response()->json(['message' => "Sản phẩm đã tồn tại trong danh sách yêu thích"], 400);
        }

        Wishlist::create([
            'user_id' => $userId,
            'product_id' => $productId,
        ]);

        return response()->json(['message' => "Thêm sản phẩm yêu thích thành công"], 201);
    }
}
