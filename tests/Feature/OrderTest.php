<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase; // Reset the database for each test

    /** @test */
    public function it_can_return_all_orders()
    {
        // Create customers and orders
        $customer1 = User::factory()->create(['role' => 'CUSTOMER']);
        $customer2 = User::factory()->create(['role' => 'CUSTOMER']);

        $order1 = Order::factory()->create([
            'user_id' => $customer1->id,
            'total_price' => 150000,
            'status' => 'WAITING',
            'consume_location' => 'IN_SHOP',
        ]);

        $order2 = Order::factory()->create([
            'user_id' => $customer2->id,
            'total_price' => 200000,
            'status' => 'PREPARATION',
            'consume_location' => 'TAKE_AWAY',
        ]);

        // Retrieve all orders
        $response = $this->get('/api/orders');

        // Check if the response includes the orders data
        $response->assertStatus(200)
            ->assertJson([
                [
                    'user_id' => $customer1->id,
                    'total_price' => 150000,
                    'status' => 'WAITING',
                    'consume_location' => 'IN_SHOP',
                ],
                [
                    'user_id' => $customer2->id,
                    'total_price' => 200000,
                    'status' => 'PREPARATION',
                    'consume_location' => 'TAKE_AWAY',
                ],
            ])
            ->assertJsonCount(2); // Ensure that two orders are returned in the response.
    }

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
    public function it_can_store_a_new_order()
    {
        // Create a customer
        $customer = User::factory()->create(['role' => 'CUSTOMER']);

        // A valid order data
        $orderData = [
            'user_id' => $customer->id,
            'total_price' => 150000,
            'status' => 'WAITING',
            'consume_location' => 'IN_SHOP',
        ];

        // Send a POST request to store the order
        $response = $this->post('/api/orders', $orderData);

        // Check if the response indicates a successful creation (HTTP status code 201 Created)
        $response->assertStatus(201);

        // Check if the response includes the created order data
        $response->assertJson($orderData);

        // Check if the order is actually stored in the database
        $this->assertDatabaseHas('orders', $orderData);
    }

    /** @test */
    public function it_can_read_an_order()
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

        // Retrieve the order
        $response = $this->get("/api/orders/{$order->id}");

        // Check if the response includes the order data
        $response->assertStatus(200)
            ->assertJson([
                'user_id' => $customer->id,
                'total_price' => 150000, 
                'status' => 'WAITING',  
                'consume_location' => 'IN_SHOP', 
            ]);
    }

    /** @test */
    public function it_can_update_an_order()
    {
        // Create a order
        $order = order::factory()->create();

        // Update the order
        $response = $this->put("/api/orders/{$order->id}", [
            'total_price' => 560000, 
            'status' => 'DELIVERED',  
            'consume_location' => 'TAKE_AWAY', 
        ]);

        // Check if the order attributes have been updated in the database
        $this->assertDatabaseHas('orders', [
            'total_price' => 560000, 
            'status' => 'DELIVERED',  
            'consume_location' => 'TAKE_AWAY', 
        ]);

        // Check if the response indicates success (HTTP status code 200 for OK)
        $response->assertStatus(200);
    }

    /** @test */
    public function it_can_delete_an_order()
    {
        // Create a order
        $order = order::factory()->create();

        // Delete the order
        $response = $this->delete("/api/orders/{$order->id}");

        // Check if the order has been deleted from the database
        $this->assertDatabaseMissing('orders', ['id' => $order->id]);

        // Check if the response indicates success (HTTP status code 204 for No Content)
        $response->assertStatus(204);
    }
}
