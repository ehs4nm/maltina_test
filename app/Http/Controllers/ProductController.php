<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
/**
     * Display a listing of the products.
     *
     * @return JsonResponse
     */
    public function index()
    {
        // Retrieve all products from the database
        $products = Product::all();

        // Return a JSON response with the list of products
        return response()->json($products, 200);
    }

    /**
     * Store a newly created product in storage.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        // Store a newly created product in storage.
        // Validate the incoming request data.
        $validatedproductData = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|integer|min:0|max:999999',
        ]);

        $product = Product::create($validatedproductData);

        // Return a JSON response with the newly created product and HTTP status code 201 (Created)
        return response()->json($product, 201);
    }

    /**
     * Display the specified product.
     *
     * @param  Product  $product
     * @return JsonResponse
     */
    public function show(Product $product)
    {
        // Return a JSON response with the specified product
        return response()->json($product, 200);
    }

    /**
     * Update the specified product in storage.
     *
     * @param  Request  $request
     * @param  Product  $product
     * @return JsonResponse
     */
    public function update(Request $request, Product $product)
    {
        // Update the product's attributes with the validated request data
        $validUpdatedProduct = $request->validate([
            'name' => 'required|string|max:255',
            'slug'=> 'required|string|max:255',
            'price' => 'required|integer|max:999999|min:0',
        ]);

        $product->update($validUpdatedProduct);

        // Return a JSON response with the updated product
        return response()->json($product, 200);
    }

    /**
     * Remove the specified product from storage.
     *
     * @param  Product  $product
     * @return JsonResponse
     */
    public function destroy(Product $product)
    {
        // Delete the specified product from the database
        $product->delete();

        // Return a JSON response with a success message and HTTP status code 204 (No Content)
        return response()->json(null, 204);
    }
}
