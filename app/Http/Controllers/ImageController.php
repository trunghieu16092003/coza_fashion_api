<?php

namespace App\Http\Controllers;

use App\Models\ProductImage;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function index($id)
    {
        $query = ProductImage::select("product_images.*", 'products.*')->join("products", 'product_images.product_id', '=', 'products.pid')
            ->where("product_images.product_id", "=", $id)->orderBy('product_images.id', 'desc');

        $images =  $query->get();

        $result = $images->map(function ($image) {
            return [
                'id' => $image->id,
                'path' => $image->path,
            ];
        });
        return response()->json(['data' => $result], 200);
    }

    public function add(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif',
                'product_id' => 'required',
            ]);

            $product_id = $request->input('product_id');

            $images = ProductImage::select("product_images.*", 'products.*')->join("products", 'product_images.product_id', '=', 'products.pid')
                ->where("product_images.product_id", "=", $product_id)->orderBy('product_images.id', 'desc')->get();

            if (count($images) >= 4) {
                return response()->json(['message' => "Số lượng ảnh đã đạt tối đa"], 400);
            }

            $image = $request->file('image');
            $imageName = time() . '.' . $image->extension();
            $image->move(public_path('uploads'), $imageName);
            if ($image)
                ProductImage::create([
                    'product_id' => $product_id,
                    'path' =>  $imageName,
                ]);
            return response()->json(['success' => true, 'message' => "Thêm hình ảnh thành công"], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => "Thêm ảnh thất bại"], 500);
        }
    }


    public function delete($id)
    {
        $image = ProductImage::find($id);
        if (!$image) {
            return response()->json(['message' => 'Không tìm thấy ảnh'], 404);
        }
        $image->delete();
        return response()->json(['message' => 'Xóa ảnh thành công'], 200);
    }
}
