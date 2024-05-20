<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();

        if (count($categories) <= 0) {
            return response()->json([
                'msg' =>  'No categories found'
            ], 404);
        }

        return response()->json([
            "data" => [
                'msg' => 'Categories retrieved successfully',
                'data' => $categories
            ]
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => "required|max:255|unique:categories,name"
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }

        $validated = $validator->validated();

        $category = Category::create([
            'name' => $validated['name']
        ]);

        return response()->json([
            "data" => [
                'msg' => 'Category created successfully',
                'data' => $category
            ]
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                'msg' =>  'Category not found'
            ], 404);
        }
        $validator = Validator::make($request->all(), [
            'name' => "required|max:255|unique:categories,name"
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }

        $validated = $validator->validated();

        $category->update([
            'name' => $validated['name']
        ]);

        return response()->json([
            "data" => [
                'msg' => 'Category updated successfully',
                'data' => $category
            ]
        ], 200);
    }

    public function destroy($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                'msg' =>  'Category not found'
            ], 404);
        }
        $category->delete();
        return response()->json([
            'msg' => 'Category deleted successfully'
        ]);
    }
}
