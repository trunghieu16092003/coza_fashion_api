<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $pageSize = 10;

        $query = Product::select('products.*', 'categories.*')
            ->join('categories', 'products.cat_id', '=', 'categories.id')
            ->orderBy('products.pid', 'desc');

        if (request()->has('keyword')) {
            $search = request('keyword');
            $result = $query->where('p_name', 'like', '%' . $search . '%')
                ->orWhere('pid', $search)
                ->get();

            $result = $this->processImages($result);

            if (request()->has('page')) {
                $products = $query->paginate($pageSize);
                $result = $this->processImages($products->items());
            }
        } else if (request()->has('page')) {
            $products = $query->paginate($pageSize);
            $result = $this->processImages($products->items());
        } else {
            $result = $this->processImages($query->get());
        }

        return response()->json(['data' => $result], 200);
    }

    protected function processImages($items)
    {
        $items = is_array($items) ? collect($items) : $items;

        return $items->map(function ($item) {
            $item->images = ProductImage::where('product_id', $item->pid)->pluck('path')->toArray();
            return $item;
        });
    }

    public function getProductById($id)
    {
        $products = Product::select('products.*', 'categories.*')
            ->join('categories', 'products.cat_id', '=', 'categories.id')->where('products.pid', '=', $id)->first();
        return response()->json(['data' => [$products]], 200);
    }

    public function addProduct(Request $request)
    {
        $lowercase = strtolower($request->input('p_name'));
        $slug = Str::slug($lowercase, '-');
        Product::create([
            'cat_id' => $request->input('cat_id'),
            'p_name' => $request->input('p_name'),
            'p_desc' => $request->input('p_desc'),
            'p_price' => $request->input('p_price'),
            'discount' => $request->input('discount'),
            'slug' => $slug,
            'rating' => 0,
            "is_disabled" => 1
        ]);
        $lowercase = strtolower($request->input('p-name'));

        $slug = preg_replace('/[^a-z0-9]/', '-', $lowercase);
        return response()->json(['message' => "Thêm sảm phẩm thành công"], 201);
    }


    public function updateProduct(Request $request, $id)
    {
        $product = Product::find($id);

        $lowercase = strtolower($request->input('p-name'));
        $slug = preg_replace('/[^a-z0-9]/', '-', $lowercase);

        if (!$product) {
            return response()->json(['message' => 'Không tìm thấy sản phẩm'], 404);
        }

        $product->update([
            'cat_id' => $request->input('cat_id'),
            'p_name' => $request->input('p_name'),
            'p_desc' => $request->input('p_desc'),
            'p_price' => $request->input('p_price'),
            'discount' => $request->input('discount'),
            'slug' => $slug,
        ]);

        return response()->json(['message' => 'Cập nhật sản phẩm thành công'], 200);
    }

    public function deleteProduct(Request $request, $id)
    {
        $product = Product::find($id);

        echo $request->input("is_disabled");

        if (!$product) {
            return response()->json(['message' => 'Không tìm thấy sản phẩm'], 404);
        }

        $product->update([
            "is_disabled" => $request->input("is_disabled")
        ]);

        return response()->json(['message' => 'Xóa sản phâm thành công'], 200);
    }
}
