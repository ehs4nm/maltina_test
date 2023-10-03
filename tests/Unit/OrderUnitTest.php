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
}
