<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Option;
use App\Models\Product;
use App\Models\Type;
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
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Retrieve all products from the database
        $products = Product::paginate(10);
        
        // Return a view with the list of products
        return view('dashboard.products.index', ['products' => $products]);
    }

    /**
     * Show the form for creating a new product.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Display the product creation form.
        // Load the types for the view
        $types = Type::with('options')->get();
        
        // return a view that includes a form for creating a new product.
        return view('dashboard.products.create', ['types' => $types]);
    }

    /**
     * Store a newly created product in storage.
     *
     * @param  App\Http\Requests\ProductRequest  $request
     * @return \Illuminate\View\View
     */
    public function store(ProductRequest $request)
    {
        // Store a newly created product in storage.

        // Validate the incoming request data thourogh ProductRequest and Retrieve the validated input data.
        $validatedProductData = $request->validated();

        // Create and store the new order using the ProductService.
        $product = $this->productService->createProduct($validatedProductData);

        // Return a view with the newly created product.
        return redirect()->route('products.index');
    }

    /**
     * Display the specified product.
     *
     * @param  App\Models\Product  $product
     * @return \Illuminate\View\View
     */
    public function show(Product $product)
    {
        // Return a view with the specified product
        return view('dashboard.products.show', ['product' => $product]);
    }

    /**
     * Show the form for editing the product.
     *
     * @param  App\Models\Product  $product
     * @return \Illuminate\View\View
     */
    public function edit(Product $product)
    {
        // Load the types for the view
        $types = Type::with('options')->get();
        
         // return a view that includes a form for editing the product.
        return view('dashboard.products.edit', ['product' => $product, 'types' => $types]);
    }

    /**
     * Update the specified product in storage.
     *
     * @param  App\Http\Requests\ProductRequest  $request
     * @param  App\Models\Product  $product
     * @return \Illuminate\View\View
     */
    public function update(ProductRequest $request, Product $product)
    {
        // Update the product's attributes with the validated request data

        // Validate the incoming request data thourogh OrderRequest and Retrieve the validated input data.
        $validUpdatedProductData = $request->validated();

        $updatedProduct = $this->productService->updateProduct($product, $validUpdatedProductData);

        // Return a JSON response with the updated product
        if($updatedProduct !== null)
            // Return a redirect to products list.
            return redirect()->route('products.index')->with('success', "You have updated the product successfuly"); 
        else 
            // Return index view with an error.
            return redirect()->route('products.index')->with('errors', "You don't have the permission to update this product");
    }

    /**
     * Remove the specified product from storage.
     *
     * @param  App\Models\Product  $product
     * @return \Illuminate\View\View
     */
    public function destroy(Product $product)
    {
        // Delete the specified product using the ProductService.
        $productSoftDeleted = $this->productService->deleteProduct($product);

        if($productSoftDeleted === true)
            // Return a redirect with a success message.
            return redirect()->route('products.index')->with('success', "You have deleted the product successfuly"); 
        else
            // Return index view with an error.
            return redirect()->route('products.index')->with('errors', "You don't have the permission to delete this product");
    }
}
