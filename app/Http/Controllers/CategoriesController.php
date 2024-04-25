<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoriesController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return response()->json(['data' => $categories], 200);
    }

    public function getCategoryById($id)
    {
        $categories = Category::find($id);
        return response()->json(['data' => [$categories]], 200);
    }

    public function addCategory(Request $request)
    {

        Category::create([
            'name' => $request->input('name'),

        ]);

        return response()->json(['message' => "Thêm danh mục thành công"], 201);
    }

    public function updateCategory(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $category->update([
            'name' => $request->input('name'),
        ]);

        return response()->json(['message' => 'Category updated successfully'], 200);
    }


    public function deleteCategory($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $category->delete();

        return response()->json(['message' => 'Category deleted successfully'], 200);
    }

    public function search($search)
    {
        $categories = Category::where('name', 'like', '%' . $search . '%')
            ->orWhere('id', $search)
            ->get();

        if ($categories->isEmpty()) {
            return response()->json(['message' => 'No matching categories found'], 404);
        }

        return response()->json(['data' => $categories], 200);
    }
}
