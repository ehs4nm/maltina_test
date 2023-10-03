<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderUnitTest extends TestCase
{
    use RefreshDatabase; // Reset the database for each test

    /** @test */
    public function it_can_create_an_order()
    {
        // Create a customer
        $customer = User::factory()->create(['role' => 'CUSTOMER']);
        
        // Create an order
        $order = Order::create([
            'user_id' => $customer->id,
            'total_price' => 150000, 
            'status' => 'WAITING',  
            'consume_location' => 'IN_SHOP', 
        ]);

        // Check if the order exists in the database
        $this->assertDatabaseHas('orders', [
            'user_id' => $customer->id,
            'total_price' => 150000, 
            'status' => 'WAITING',  
            'consume_location' => 'IN_SHOP', 
        ]);

        // Check order attributes
        $this->assertEquals($customer->id, $order->user_id);
        $this->assertEquals(150000, $order->total_price);
        $this->assertEquals('WAITING', $order->status);
        $this->assertEquals('IN_SHOP', $order->consume_location);
    }

    /** @test */
    public function an_order_belongs_to_a_user()
    {
        // Create a user and an order associated with that user
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        // Retrieve the user's orders
        $userOrders = $user->orders;

        // Assert that the user has one order and it matches the created order
        $this->assertCount(1, $userOrders);
        $this->assertTrue($userOrders->contains($order));
    }

    /** @test */
    public function a_user_has_many_orders()
    {
        // Create a user and multiple orders associated with that user
        $user = User::factory()->create();
        $orders = Order::factory(3)->create(['user_id' => $user->id]);

        // Retrieve the user's orders
        $userOrders = $user->orders;

        // Assert that the user has three orders and they match the created orders
        $this->assertCount(3, $userOrders);
        foreach ($orders as $order) {
            $this->assertTrue($userOrders->contains($order));
        }
    }
}
