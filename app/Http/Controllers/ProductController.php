<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Traits\Error;
use App\Traits\Helpers;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use Error, Helpers;

    public function index(Request $request)
    {
        try {
            $products = Product::all();
            return response()->json($products);
        } catch (\Exception $error) {
            return $this->errorResponse($error);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string'],
                'description' => ['required', 'string'],
                'price' => ['required', 'numeric', 'min:0'],
                'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            ]);

            $product = Product::store($request);

            return response()->json($product, 201);
        } catch (\Exception $error) {
            return $this->errorResponse($error);
        }
    }

    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'sometimes'],
                'description' => ['required', 'string', 'sometimes'],
                'price' => ['required', 'numeric', 'min:0', 'sometimes'],
                'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            ]);

            $product = Product::find($request->productId);
            if (!$product) {
                throw new \Exception('Error|Product not found--404', 13333);
            }

            $product = Product::store($request, $request->productId);

            return response()->json($product);
        } catch (\Exception $error) {
            return $this->errorResponse($error);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $product = Product::find($request->productId);
            if (!$product) {
                throw new \Exception('Error|Product not found--404', 13333);
            }

            $product->delete();

            return response()->json(['message' => 'Product deleted successfully']);
        } catch (\Exception $error) {
            return $this->errorResponse($error);
        }
    }
}
