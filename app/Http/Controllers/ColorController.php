<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Color;

class ColorController extends Controller
{
    public function index()
    {
        $colors = Color::all();
        return response()->json(['data' => $colors], 200);
    }

    public function getColorById($id)
    {
        $colors = Color::find($id);
        return response()->json(['data' => $colors], 200);
    }

    public function addColor(Request $request)
    {

        Color::create([
            'cat_name' => $request->input('cat_name'),

        ]);

        return response()->json(['message' => "Thêm danh mục thành công"], 201);
    }

    public function updateColor(Request $request, $id)
    {
        $colors = Color::find($id);

        if (!$colors) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $colors->update([
            'cat_name' => $request->input('cat_name'),
        ]);

        return response()->json(['message' => 'Category updated successfully'], 200);
    }


    public function deleteCategory($id)
    {
        $colors = Color::find($id);

        if (!$colors) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $colors->delete();

        return response()->json(['message' => 'Category deleted successfully'], 200);
    }
}
