<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Type;

class ProductService
{
    /**
     * Create a new product.
     *
     * @param  array  $validatedProductData
     * @return Product|null
     */
    public function createProduct(array $validatedProductData): ?Product
    {
        // Create and store the new Product
        $typeName = $validatedProductData['type'];

        // Check if the name of type user sent is created before or is new
        if (! Type::where('name', $typeName)->exists()) 
            // Create type if it is new
            $type = Type::create(['name' => $typeName]);
        else  $type = Type::where('name', $typeName)->first(); // set type if type existed before

        $validatedProductData['type_id'] = $type->id; // set type_id in $validatedProductData
        $product = Product::create($validatedProductData);

        return $product;
    }

    /**
     * Update a product.
     *
     * @param  Product  $product
     * @param  array    $validatedUpdateProductData
     * @return Product|null
     */
    public function updateProduct(Product $product, array $validatedUpdateProductData): ?Product
    {
        // Update the specified product based on the user's role.
        dd($validatedUpdateProductData);
        if (auth()->user()->role === 'MANAGER') {
            $product->update($validatedUpdateProductData);
            return $product;
        }

        // If the update is not successful, return null.
        return null;
    }

    /**
     * Delete a product.
     *
     * @param  Product  $product
     * @return bool
     */
    public function deleteProduct(Product $product): bool
    {
        // Delete the specified product from the database if the user is a manager.
        if (auth()->user()->role === 'MANAGER') {
            $product->delete();
            return true;
        }

        // If the user is not a manager, return false.
        return false;
    }
}