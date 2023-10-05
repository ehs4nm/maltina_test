<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
        $this->authorizeResource(Product::class, 'product');
    }
    /**
     * Display a listing of the products.
     *
     * @return JsonResponse
     */
    public function index()
    {
        // Retrieve all products from the database
        $products = Product::paginate(10);
        
        // Return a JSON response with the list of products
        return view('dashboard.products.index', ['products' => $products]);
    }

    /**
     * Store a newly created product in storage.
     *
     * @param  App\Http\Requests\ProductRequest  $request
     * @return JsonResponse
     */
    public function store(ProductRequest $request)
    {
        // Store a newly created product in storage.

        // Validate the incoming request data thourogh ProductRequest and Retrieve the validated input data.
        $validatedProductData = $request->validated();

        // Create and store the new order using the ProductService.
        $product = $this->productService->createProduct($validatedProductData);

        // Return a JSON response with the newly created product and HTTP status code 201 (Created)
        return response()->json($product, 201);
    }

    /**
     * Display the specified product.
     *
     * @param  App\Models\Product  $product
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
     * @param  App\Http\Requests\ProductRequest  $request
     * @param  App\Models\Product  $product
     * @return JsonResponse
     */
    public function update(ProductRequest $request, Product $product)
    {
        // Update the product's attributes with the validated request data

        // Validate the incoming request data thourogh OrderRequest and Retrieve the validated input data.
        $validUpdatedProductData = $request->validated();

        $updatedProduct = $this->productService->updateProduct($product, $validUpdatedProductData);

        // Return a JSON response with the updated product
        if($updatedProduct !== null)
            // Return a JSON response with a success message and HTTP status code OK.
            return response()->json($product, 200); 
        else 
            return response()->json(null, 403);
    }

    /**
     * Remove the specified product from storage.
     *
     * @param  App\Models\Product  $product
     * @return JsonResponse
     */
    public function destroy(Product $product)
    {
        // Delete the specified product using the ProductService.
        $productSoftDeleted = $this->productService->deleteProduct($product);

        if($productSoftDeleted === true)
            // Return a JSON response with a success message and HTTP status code 204 (No Content).
            return response()->json(null, 204); 
        else
            // Return a JSON response forbidden.
            return response()->json(null, 403);
    }
}
