<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Product::class, 'product');
    }
    
    public function index()
    {
        // Retrieve all products from the database
        $products = Product::all();

        // Return a JSON response with the list of products
        return ProductResource::collection($products);
    }
}
