<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        $product = Product::all();
        if ($product->count() <= 0) {
            return response()->json([
                'msg' =>  'Product not found',
            ], 404);
        }

        return response()->json([
            "data" => [
                'msg' => "{$product->count()} product found",
                'data' => $product
            ]
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255|unique:products,name',
            'price' => 'required|numeric',
            'description' => 'max:2048',
            'expired_at' => 'required|date',
            'image' => 'max:2048|mimes:jpeg,png,jpg,gif,svg',
            'category_id' => 'required|exists:categories,name',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }
        $validated = $validator->validated();

        $category_id = Category::where('name', $validated['category_id'])->first()->id;

        $image = $request->file('image');
        $fileName = now()->timestamp . '_' . $image->getClientOriginalExtension();
        $image->move('Uploads/', $fileName);

        $product = Product::create([
            'name' => $validated['name'],
            'price' => $validated['price'],
            'description' => $validated['description'],
            'image' => 'Uploads/'.$fileName,
            'category_id' => $category_id,
            'expired_at' => $validated['expired_at'],
            'modified_by' => $request['email']
        ]);

        return response()->json([
            "data" => [
                'msg' => 'Product created successfully',
                'data' => $product
            ]
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'msg' =>  'Product not found',
            ],);
        }
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'max:255',
                Rule::unique('products')->ignore($id),
            ],
            'price' => 'required|numeric',
            'description' => 'max:2048',
            'expired_at' => 'required|date',
            'image' => 'max:2048|mimes:jpeg,png,jpg,gif,svg',
            'category_id' => 'required|exists:categories,name',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }

        $validated = $validator->validated();
        $category_id = Category::where('name', $validated['category_id'])->first()->id;

        $image = $request->file('image');
        $fileName = now()->timestamp . '_' . $image->getClientOriginalExtension();
        $image->move('Uploads/', $fileName);

        $product->update([
            'name' => $validated['name'],
            'price' => $validated['price'],
            'description' => $validated['description'],
            'image' => 'Uploads/'.$fileName,
            'category_id' => $category_id,
            'expired_at' => $validated['expired_at'],
            'modified_by' => $request['email']
        ]);

        return response()->json([
            "data" => [
                'msg' => 'Product updated successfully',
                'data' => $product
            ]
        ], 200);

    }

    public function destroy($id)
    {

        $product = Product::find($id);
        if (!$product) {
            return response()->json([
                'msg' =>  'Product not found',
            ], 404);
        }

        $product->delete();
        return response()->json([
                'msg' =>  'Product deleted successfully',
        ], 200);
    }
}
