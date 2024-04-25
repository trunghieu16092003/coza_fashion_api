<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\ProductInventory;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class CartController extends Controller
{
    public function addToCart(Request $request)

    {
        $productId = $request->input('pid');
        $quantity = $request->input('quantity');
        $sizeId = $request->input('size');
        $colorId = $request->input('color');

        $productInventory = ProductInventory::where('product_id', $productId)
            ->where('size_id', $sizeId)
            ->where('color_id', $colorId)
            ->first();

        $quantityBuy = $productInventory->quantity_buy;

        $existingCart = Cart::where('user_id', auth()->user()->id)->where('pid', $productId)->first();

        if ($quantity < $quantityBuy) {
            if ($existingCart) {
                $existingCart->quantity += $quantity;
                $existingCart->save();
            } else {
                Cart::create([
                    'user_id' => auth()->user()->id,
                    'pid' => $productId,
                    'quantity' => $quantity,
                    'size_id' => $sizeId,
                    'color_id' => $colorId
                ]);
            }
            return response()->json(['message' => 'Thêm sản phẩm vào giỏ hàng thành công'], 201);
        } else {
            return response()->json(['message' => 'Sản phẩm tồn kho có số lượng là ' . $quantityBuy], 400);
        }
    }

    public function index()
    {
        $userId = auth()->user()->id;

        $cartItems = Cart::select('carts.*', 'products.*')
            ->join('products', 'carts.pid', '=', 'products.pid')
            ->where('carts.user_id', '=', $userId)
            ->get();

        return response()->json(['data' => $cartItems]);
    }

    public function update_cart(Request $request, $id)
    {
        $carts = Cart::find($id);

        $productId = $request->input('pid');
        $quantity = $request->input('quantity');
        $sizeId = $request->input('size_id');
        $colorId = $request->input('color_id');

        $productInventory = ProductInventory::where('product_id', $productId)
            ->where('size_id', $sizeId)
            ->where('color_id', $colorId)
            ->first();

        $quantityBuy = $productInventory->quantity_buy;

        if (!$carts) {
            return response()->json(['message' => 'Không tìm thấy giỏ'], 404);
        }

        if ($quantity < $quantityBuy) {
            $updateData = $request->only([
                'pid',
                'size_id',
                'color_id',
                'quantity',
            ]);

            $carts->update($updateData);
            return response()->json(['message' => 'Cập nhật thành công'], 200);
        } else {
            return response()->json(['message' => 'Sản phẩm tồn kho có số lượng là ' . $quantityBuy], 400);
        }
    }

    public function delete($id)
    {
        $carts = Cart::find($id);
        $carts->delete();
        return response()->json(['message' => 'Xóa giỏ hàng thành công'], 200);
    }
}
