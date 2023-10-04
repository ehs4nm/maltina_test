<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Option;
use App\Models\Order;
use App\Models\Product;
use App\Models\Type;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CartUnitTest extends TestCase
{
    use RefreshDatabase; // Reset the database for each test

    /** @test */
    public function it_can_create_a_cart_with_an_order_assigned()
    {
        // Create a cart, an order
        $cart = Cart::factory()->create();
        $order = Order::factory()->create();

        // Associate the cart with the order
        $cart->order()->associate($order);
        
        // Save the changes
        $cart->save();

        // Check if the cart is associated with the order
        $this->assertEquals($order->id, $cart->order->id);
    }

    /** @test */
    public function it_can_create_a_cart_with_multiple_products_assigned()
    {
        // Create a cart, an order, and some products and a type
        $cart = Cart::factory()->create();
        $order = Order::factory()->create();
        $type = Type::factory()->create();
        $products = Product::factory(3)->create(['type_id' => $type->id]);
        // Create a random option for the product
        $randomOption = Option::factory(3)->create(['type_id' => $type->id]);
        // Create a random quantity for the product
        $quantity = [rand(1, 5), rand(1, 5), rand(1, 5)];

        // Associate the cart with the order and Save the changes
        $cart->order()->associate($order);
        $cart->save();

        // Loop through each product and associate it with the cart
        foreach ($products as $key => $product) {
            // Attach the product to the cart and set the specified attributes
            $cart->products()->attach($product->id, [
                'quantity' => $quantity[$key], 
                'sum_price' => $quantity[$key] * $product->price,
                'option_id' => $randomOption[$key]->id
            ]);
        }

        foreach ($products as $key => $product) {
            // Check if the product is associated with the cart
            $this->assertTrue($cart->products->contains($product));

            // Check if the associated quantity matches the assigned quantity
            $this->assertEquals($quantity[$key], $cart->products->find($product->id)->pivot->quantity);

            // // Check if the associated option matches the assigned option
            $this->assertEquals($randomOption[$key]->id, $cart->products->find($product->id)->pivot->option_id);
        }

        // Check if the cart is associated with the order
        $this->assertEquals($order->id, $cart->order->id);
    }
}
