<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductInventory;
use Illuminate\Support\Facades\DB;

class ProductInventoryController extends Controller
{
    public function index($productId)
    {

        $pageSize = 3;

        $query = ProductInventory::select('product_inventories.*', 'colors.*', 'sizes.*')
            ->join('colors', 'product_inventories.color_id', '=', 'colors.id')
            ->join('sizes', 'product_inventories.size_id', '=', 'sizes.id')
            ->where('product_inventories.product_id', '=', $productId)
            ->orderBy('product_inventories.id', 'desc');

        $inventories = request()->has('page') ? $query->paginate($pageSize) : $query->get();

        $result = $inventories->map(function ($inventory) {
            return [
                'id' => $inventory->id,
                'color' => $inventory->color->color_name,
                'code' => $inventory->color->code,
                'color_id' => $inventory->color_id,
                'size_id' => $inventory->size_id,
                'size' => $inventory->size->name,
                'quantity_buy' => $inventory->quantity_buy,
                'quantity_sold' => $inventory->quantity_sold,

            ];
        });

        return response()->json(['data' => $result], 200);
    }

    public function getInventoryById($productId, $inventoryId)
    {
        $inventory = ProductInventory::select('product_inventory.*', 'colors.*', 'sizes.*', 'product_inventory.id as product_inventory_id')
            ->join('colors', 'product_inventory.color_id', '=', 'colors.id')
            ->join('sizes', 'product_inventory.size_id', '=', 'sizes.id')
            ->where('product_inventory.product_id', '=', $productId)
            ->where('product_inventory.id', '=', $inventoryId)
            ->first();

        if (!$inventory) {
            return response()->json(['message' => 'Không tìm thấy hàng tồn kho'], 404);
        }

        $result = [
            'id' => $inventory->product_inventory_id,
            'color' => $inventory->color->color_name,
            'size' => $inventory->size->name,
            'quantity_buy' => $inventory->quantity_buy,
            'quantity_sold' => $inventory->quantity_sold,
        ];

        return response()->json(["data" => [$result]], 200);
    }

    public function add(Request $request)
    {
        try {
            $existingRecord = ProductInventory::where('color_id', $request->input('color_id'))
                ->where('size_id', $request->input('size_id'))
                ->first();

            if ($existingRecord) {
                return response()->json(['success' => false, 'message' => "Color và Size đã tồn tại"], 400);
            }

            ProductInventory::create([
                'product_id' => $request->input('product_id'),
                'color_id' => $request->input('color_id'),
                'size_id' => $request->input('size_id'),
                'quantity_buy' => $request->input('quantity_buy'),
                "quantity_sold" => 0
            ]);

            return response()->json(['success' => true, 'message' => "Thêm hàng tồn kho thành công"], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => "Thêm hàng tồn kho thất bại"], 500);
        }
    }
    public function update(Request $request, $id, $inventoryId)
    {
        $inventory = ProductInventory::select('id', 'quantity_buy', 'quantity_sold')
            ->with(['color' => function ($query) {
                $query->select('id', 'color_name');
            }])
            ->with(['size' => function ($query) {
                $query->select('id', 'name');
            }])
            ->where('product_id', $id)
            ->where('id', $inventoryId);

        $inventory->update([
            'color_id' => $request->input('color_id'),
            'size_id' => $request->input('size_id'),
            'quantity_buy' => $request->input('quantity_buy'),
        ]);

        return response()->json(['message' => 'Inventory updated successfully'], 200);
    }


    public function delete($id)
    {
        $productInventory = ProductInventory::find($id);
        if (!$productInventory) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        $productInventory->delete();
        return response()->json(['message' => 'Product deleted successfully'], 200);
    }
}
