<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\Option;

class OrderService
{
    public function createOrder(array $validatedOrderData): ?Order
    {
        // Create and store the new order using the validated data.
        $order = Order::create($validatedOrderData);
        $cart = Cart::create(['order_id' => $order->id]);

        // A var to save the total_price for order
        $total_price = 0;

        // Attach the products with chosen options to the order
        foreach ($validatedOrderData['products'] as $productData) {
            $product = Product::find($productData['product_id']);
            $option = Option::find($productData['option_id']);
            
            // Attach the product to the order and set the quantity and option
            $cart->products()->attach($product->id, [
                'option_id' => $option->id,
                'quantity'  => $productData['quantity'],
                'sum_price' => $productData['quantity'] * $product->price,
            ]);
    
            // Sum of the price of each product * quantity
            $total_price +=  $product->price * $productData['quantity'];
        }
        $order->update(['total_price' => $total_price]);

        // Load the relations
        $order = Order::with('cart.products')->find($order->id);
        
        return $order;
    }

    public function updateOrder(Order $order, array $validatedUpdateOrderData): ?Order
    {
        // Update the specified order based on the role and status.
        // Rest of the logic for updating the order
        
        if(auth()->user()->role === 'CUSTOMER' && $order->status === 'WAITING') {
            $order->update($validatedUpdateOrderData); // Update the specified order if user is a customer and order is waiting.
            return $order; // Return a JSON response with a success message and HTTP status code OK.
        }
        
        if(auth()->user()->role === 'MANAGER') {
            $order->update($validatedUpdateOrderData); // Update the specified order if user is a manager.
            return $order; // Return a JSON response with a success message and HTTP status code OK.
        }

        // If not successful return null
        return null;
    }

    public function deleteOrder(Order $order): bool
    {
        // Delete the specified order based on the role and status.
        if(auth()->user()->role === 'CUSTOMER' && $order->status === 'WAITING') {
            $order->delete(); // Delete the specified order from the database if user is a customer and order is waiting.
            return true; // Return true So controller would send a JSON response with a success message
        }
        
        if(auth()->user()->role === 'MANAGER') {
            $order->delete(); // Delete the specified order from the database if user is a manager.
            return true; // Return true So controller would send a JSON response with a success message
        }
        
        return false;
    }
}