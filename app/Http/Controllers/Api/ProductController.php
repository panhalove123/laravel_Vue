<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\storeProductRequest;
use App\Models\Product;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all();
        return response()->json([
            'message' => 'List of products',
            'data' => $products
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'image' => 'required|image|max:2048',
        ]);

        $image = $request->file('image');
        $filename = time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('image'), $filename);

        $product = new Product();
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->image = 'image/' . $filename;
        $product->save();

        return response()->json(['message' => 'Product created successfully', 'data' => $product]);
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::find($id)->select('id', 'name', 'description', 'price')->first();

        return response()->json([
            'message' => 'Product details',
            'data' => $product
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(storeProductRequest $request, string $id)
    {
        $product = Product::store($request, $id);

        return response()->json([
            'message' => 'Product updated successfully',
            'data' => $product
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);

        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully'
        ]);
    }

    /**
     * Upload an image for a product and store it in public/image.
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:2048',
        ]);

        $image = $request->file('image');
        $filename = time() . '.' . $image->getClientOriginalExtension();

        // Store the image in storage/app/public/image directory
        $path = $image->storeAs('public/image', $filename);

        return response()->json([
            'message' => 'Image uploaded successfully',
            'image_path' => 'storage/image/' . $filename
        ]);
    }
}
