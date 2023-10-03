<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase; // Reset the database for each test

    /** @test */
    public function a_manager_can_return_all_orders()
    {
        // Create a manager and generate a Sanctum token
        $manager = User::factory()->create(['role' => 'MANAGER']);
        $token = $manager->createToken('api-token')->plainTextToken;
        
        // Set the Sanctum token on the request headers
        $headers = ['Authorization' => "Bearer $token"];

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
        $response = $this->withHeaders($headers)->get('/api/orders');

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
    public function a_customer_cannot_return_all_orders_not_his()
    {
        // Create a customer and generate a Sanctum token
        $customer = User::factory()->create(['role' => 'CUSTOMER']);
        $token = $customer->createToken('api-token')->plainTextToken;
        
        // Set the Sanctum token on the request headers
        $headers = ['Authorization' => "Bearer $token"];

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
        $response = $this->withHeaders($headers)->get('/api/orders');

        // Check if the response is 403 (not allowed)
        $response->assertStatus(403); 
    }

    /** @test */
    public function a_customer_can_retrive_all_his_own_orders()
    {
        // Create a customer and generate a Sanctum token
        $customerOne = User::factory()->create(['role' => 'CUSTOMER']);
        $token = $customerOne->createToken('api-token')->plainTextToken;
        
        // Set the Sanctum token on the request headers
        $headers = ['Authorization' => "Bearer $token"];

        // Create customers and orders
        $customerTwo = User::factory()->create(['role' => 'CUSTOMER']);
        $customerThree = User::factory()->create(['role' => 'CUSTOMER']);

        $customerOneOrders = Order::factory(3)->create([
            'user_id' => $customerOne->id,
            'total_price' => round(fake()->numberBetween(100000, 900000) /1000) * 1000,
            'status' => fake()->randomElement(['WAITING','PREPARATION','READY','DELIVERED']),
            'consume_location' => fake()->randomElement(['TAKE_AWAY', 'IN_SHOP']),
        ]);

        $customerTwoOrders = Order::factory(3)->create([
            'user_id' => $customerTwo->id,
            'total_price' => round(fake()->numberBetween(100000, 900000) /1000) * 1000,
            'status' => fake()->randomElement(['WAITING','PREPARATION','READY','DELIVERED']),
            'consume_location' => fake()->randomElement(['TAKE_AWAY', 'IN_SHOP']),
        ]);
            
        $customerThreeOrders = Order::factory(3)->create([
            'user_id' => $customerThree->id,
            'total_price' => round(fake()->numberBetween(100000, 900000) /1000) * 1000,
            'status' => fake()->randomElement(['WAITING','PREPARATION','READY','DELIVERED']),
            'consume_location' => fake()->randomElement(['TAKE_AWAY', 'IN_SHOP']),
        ]);

        // Retrieve all customerOne's orders
        $response = $this->withHeaders($headers)->get("/api/users/{$customerOne->id}/orders/");

        // Check if the response includes the orders data
        $response->dump()->assertStatus(200)
            ->assertJson([
                [
                    'user_id' => $customerOne->id,
                    'total_price' => $customerOneOrders[0]->total_price,
                    'status' =>  $customerOneOrders[0]->status,
                    'consume_location' =>  $customerOneOrders[0]->consume_location,
                ],
                [
                    'user_id' => $customerOne->id,
                    'total_price' =>  $customerOneOrders[1]->total_price,
                    'status' =>  $customerOneOrders[1]->status,
                    'consume_location' =>  $customerOneOrders[1]->consume_location,
                ],
                [
                    'user_id' => $customerOne->id,
                    'total_price' => $customerOneOrders[2]->total_price,
                    'status' =>  $customerOneOrders[2]->status,
                    'consume_location' =>  $customerOneOrders[2]->consume_location,
                ],
            ])
            ->assertJsonCount(3); // Ensure that two orders are returned in the response.
    }

    /** @test */
    public function it_can_store_a_new_order()
    {
        // Create a customer and generate a Sanctum token
        $customer = User::factory()->create(['role' => 'CUSTOMER']);
        $token = $customer->createToken('api-token')->plainTextToken;

        // Set the Sanctum token on the request headers
        $headers = ['Authorization' => "Bearer $token"];

        // A valid order data
        $orderData = [
            'user_id' => $customer->id,
            'total_price' => 150000,
            'status' => 'WAITING',
            'consume_location' => 'IN_SHOP',
        ];

        // Send a POST request to store the order
        $response = $this->withHeaders($headers)->post('/api/orders', $orderData);

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
        // Create a customer and generate a Sanctum token
        $customer = User::factory()->create(['role' => 'CUSTOMER']);
        $token = $customer->createToken('api-token')->plainTextToken;

        // Set the Sanctum token on the request headers
        $headers = ['Authorization' => "Bearer $token"];

        // Create an order
        $order = Order::create([
            'user_id' => $customer->id,
            'total_price' => 150000, 
            'status' => 'WAITING',  
            'consume_location' => 'IN_SHOP', 
        ]);

        // Retrieve the order
        $response = $this->withHeaders($headers)->get("/api/orders/{$order->id}");

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
        // Create a customer and generate a Sanctum token
        $customer = User::factory()->create(['role' => 'CUSTOMER']);
        $token = $customer->createToken('api-token')->plainTextToken;

        // Set the Sanctum token on the request headers
        $headers = ['Authorization' => "Bearer $token"];
        
        // Create a order
        $order = order::factory()->create();

        // Update the order
        $response = $this->withHeaders($headers)->put("/api/orders/{$order->id}", [
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
        // Create a customer and generate a Sanctum token
        $customer = User::factory()->create(['role' => 'CUSTOMER']);
        $token = $customer->createToken('api-token')->plainTextToken;

        // Set the Sanctum token on the request headers
        $headers = ['Authorization' => "Bearer $token"];
        
        // Create a order
        $order = order::factory()->create();

        // Delete the order
        $response = $this->withHeaders($headers)->delete("/api/orders/{$order->id}");

        // Check if the order has been deleted from the database
        $this->assertDatabaseMissing('orders', ['id' => $order->id]);

        // Check if the response indicates success (HTTP status code 204 for No Content)
        $response->assertStatus(204);
    }
}
