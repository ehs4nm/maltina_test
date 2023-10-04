<?php

namespace App\Services;

use App\Models\Product;

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
        $product = Product::create($validatedProductData);

        return $product;
    }

    /**
     * Update a product.
     *
     * @param  Product  $product
     * @param  array    $validatedUpdateOrderData
     * @return Product|null
     */
    public function updateProduct(Product $product, array $validatedUpdateOrderData): ?Product
    {
        // Update the specified product based on the user's role.
        if (auth()->user()->role === 'MANAGER') {
            $product->update($validatedUpdateOrderData);
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