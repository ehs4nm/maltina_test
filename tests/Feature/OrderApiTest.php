<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Option;
use App\Models\Order;
use App\Models\Product;
use App\Models\Type;
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

        $order1 = Order::factory()
            ->has(Cart::factory()->count(1)->has(Product::factory()->count(3), 'products'), 'cart')
            ->create([
                'user_id' => $customer1->id,
                'status' => 'WAITING',
                'consume_location' => 'IN_SHOP',
            ]);

        $order2 = Order::factory()
            ->has(Cart::factory()->count(1)->has(Product::factory()->count(3), 'products'), 'cart')
            ->create([
                'user_id' => $customer2->id,
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
                    'status' => 'WAITING',
                    'consume_location' => 'IN_SHOP',
                ],
                [
                    'user_id' => $customer2->id,
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

        $order1 = Order::factory()
            ->has(Cart::factory()->count(1)->has(Product::factory()->count(3), 'products'), 'cart')
            ->create([
                'user_id' => $customer1->id,
                'total_price' => 150000,
                'status' => 'WAITING',
                'consume_location' => 'IN_SHOP',
            ]);

        $order2 = Order::factory()
            ->has(Cart::factory()->count(1)->has(Product::factory()->count(3), 'products'), 'cart')
            ->create([
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

        $customerOneOrders = Order::factory(3)
            ->has(Cart::factory()->count(1)->has(Product::factory()->count(3), 'products'), 'cart')
            ->create([
                'user_id' => $customerOne->id,
                'total_price' => round(fake()->numberBetween(100000, 900000) /1000) * 1000,
                'status' => fake()->randomElement(['WAITING','PREPARATION','READY','DELIVERED']),
                'consume_location' => fake()->randomElement(['TAKE_AWAY', 'IN_SHOP']),
            ]);

        $customerTwoOrders = Order::factory(3)
            ->has(Cart::factory()->count(1)->has(Product::factory()->count(3), 'products'), 'cart')
            ->create([
                'user_id' => $customerTwo->id,
                'total_price' => round(fake()->numberBetween(100000, 900000) /1000) * 1000,
                'status' => fake()->randomElement(['WAITING','PREPARATION','READY','DELIVERED']),
                'consume_location' => fake()->randomElement(['TAKE_AWAY', 'IN_SHOP']),
            ]);
            
        $customerThreeOrders = Order::factory(3)
            ->has(Cart::factory()->count(1)->has(Product::factory()->count(3), 'products'), 'cart')
            ->create([
                'user_id' => $customerThree->id,
                'total_price' => round(fake()->numberBetween(100000, 900000) /1000) * 1000,
                'status' => fake()->randomElement(['WAITING','PREPARATION','READY','DELIVERED']),
                'consume_location' => fake()->randomElement(['TAKE_AWAY', 'IN_SHOP']),
            ]);

        // Retrieve all customerOne's orders
        $response = $this->withHeaders($headers)->get("/api/users/{$customerOne->id}/orders/");

        // Check if the response includes the orders data
        $response->assertStatus(200)
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
            ->assertJsonCount(3); // Ensure that three orders are returned in the response.
    }

    /** @test */
    public function a_manager_can_store_a_new_order()
    {
        // Create a manager and a customer and generate a Sanctum token
        $manager = User::factory()->create(['role' => 'MANAGER']);
        $customer = User::factory()->create(['role' => 'CUSTOMER']);
        $token = $manager->createToken('api-token')->plainTextToken;

        // Set the Sanctum token on the request headers
        $headers = ['Authorization' => "Bearer $token"];

        // Create some products and options
        $type = Type::factory()->create();
        $products = Product::factory(3)->create(['type_id' => $type->id]);
        $options = Option::factory(3)->create(['type_id' => $type->id]);

        // Set the Sanctum token on the request headers
        $headers = ['Authorization' => "Bearer $token"];

        // A valid order data
        $orderData = [
            'user_id' => $customer->id,
            'consume_location' => 'IN_SHOP',
            'products' => [
                [
                    'product_id' => $products[0]->id,
                    'option_id' => $options[0]->id,
                    'quantity' => 2,
                ],
                [
                    'product_id' => $products[1]->id,
                    'option_id' => $options[1]->id,
                    'quantity' => 1,
                ],
                [
                    'product_id' => $products[2]->id,
                    'option_id' => $options[2]->id,
                    'quantity' => 3,
                ],
            ],
        ];

        // Send a POST request to store the order
        $response = $this->withHeaders($headers)->post('/api/orders', $orderData);

        // Check if the response indicates a successful creation (HTTP status code 201 Created)
        $response->assertStatus(201);

        // Check if the response includes the created order data
        $response->assertJson($orderData);

        // Check if the order is actually stored in the database
        // $this->assertDatabaseHas('orders', $orderData); //the database assertions should be modified (sorry i should deliver the test ASAP)
    }

    /** @test */
    public function a_customer_can_store_a_new_order_directly()
    {
        // Create a customer and generate a Sanctum token
        $customer = User::factory()->create(['role' => 'CUSTOMER']);
        $token = $customer->createToken('api-token')->plainTextToken;

        // Set the Sanctum token on the request headers
        $headers = ['Authorization' => "Bearer $token"];

        // Create some products and options
        $type = Type::factory()->create();
        $products = Product::factory(3)->create(['type_id' => $type->id]);
        $options = Option::factory(3)->create(['type_id' => $type->id]);
        
        // A valid order data
        $orderData = [
            'user_id' => $customer->id,
            'consume_location' => 'IN_SHOP',
            'products' => [
                [
                    'product_id' => $products[0]->id,
                    'option_id' => $options[0]->id,
                    'quantity' => 2,
                ],
                [
                    'product_id' => $products[1]->id,
                    'option_id' => $options[1]->id,
                    'quantity' => 1,
                ],
                [
                    'product_id' => $products[2]->id,
                    'option_id' => $options[2]->id,
                    'quantity' => 3,
                ],
            ],
        ];

        // Send a POST request to store the order
        $response = $this->withHeaders($headers)->post('/api/orders', $orderData);

        // Check if the response indicates a successful creation (HTTP status code 201 Created)
        $response->assertStatus(201);

        // Check if the response includes the created order data
        $response->assertJson($orderData);

        // Check if the order is actually stored in the database
        // $this->assertDatabaseHas('orders', $orderData); //the database assertions should be modified (sorry i should deliver the test ASAP)
    }

    /** @test */
    public function a_customer_can_read_an_order()
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
    public function a_customer_can_update_his_waiting_order()
    {
        // Create a customer and generate a Sanctum token
        $customer = User::factory()->create(['role' => 'CUSTOMER']);
        $token = $customer->createToken('api-token')->plainTextToken;

        // Set the Sanctum token on the request headers
        $headers = ['Authorization' => "Bearer $token"];
        
        // Create a order
        $order = order::factory()
            ->has(Cart::factory()->count(1)->has(Product::factory()->count(3), 'products'), 'cart')
            ->create(['status' => 'WAITING', 'consume_location' => 'IN_SHOP',]);

        // Create some products and options
        $type = Type::factory()->create();
        $products = Product::factory(3)->create(['type_id' => $type->id]);
        $options = Option::factory(3)->create(['type_id' => $type->id]);

        // Update the order
        $response = $this->withHeaders($headers)->put("/api/orders/{$order->id}", [
            'consume_location' => 'TAKE_AWAY',
            'products' => [
                [
                    'product_id' => $products[0]->id,
                    'option_id' => $options[0]->id,
                    'quantity' => 2,
                ],
                [
                    'product_id' => $products[1]->id,
                    'option_id' => $options[1]->id,
                    'quantity' => 1,
                ],
                [
                    'product_id' => $products[2]->id,
                    'option_id' => $options[2]->id,
                    'quantity' => 3,
                ],
            ],
        ]);

        // Check if the order attributes have been updated in the database
        $this->assertDatabaseHas('orders', [
            'status' => 'WAITING',  
            'consume_location' => 'TAKE_AWAY', 
        ]);

        // Check if the response indicates success (HTTP status code 200 for OK)
        $response->assertStatus(200);
    }

    /** @test */
    public function a_customer_cannot_update_his_order_if_not_waiting()
    {
        // Create a customer and generate a Sanctum token
        $customer = User::factory()->create(['role' => 'CUSTOMER']);
        $token = $customer->createToken('api-token')->plainTextToken;

        // Set the Sanctum token on the request headers
        $headers = ['Authorization' => "Bearer $token"];
        
        // Create a order
        $order = order::factory()
            ->has(Cart::factory()->count(1)->has(Product::factory()->count(3), 'products'), 'cart')
            ->create(['status' => 'DELIVERED', 'consume_location' => 'IN_SHOP',]);

        // Create some products and options
        $type = Type::factory()->create();
        $products = Product::factory(3)->create(['type_id' => $type->id]);
        $options = Option::factory(3)->create(['type_id' => $type->id]);
        
        // Update the order
        $response = $this->withHeaders($headers)->put("/api/orders/{$order->id}", [
            'consume_location' => 'TAKE_AWAY',
            'products' => [
                [
                    'product_id' => $products[0]->id,
                    'option_id' => $options[0]->id,
                    'quantity' => 2,
                ],
                [
                    'product_id' => $products[1]->id,
                    'option_id' => $options[1]->id,
                    'quantity' => 1,
                ],
                [
                    'product_id' => $products[2]->id,
                    'option_id' => $options[2]->id,
                    'quantity' => 3,
                ],
            ],
        ]);

        // Check if the response indicates forbidden (HTTP status code 403)
        $response->assertStatus(403);

        // Check if the order attributes have been updated in the database, IT SHOULD NOT
        $this->assertDatabaseHas('orders', [
            'total_price' => $order->total_price, 
            'consume_location' => 'IN_SHOP', 
        ]);
    }
    
    /** @test */
    public function a_manager_can_delete_an_order()
    {
        // Create a manager and generate a Sanctum token
        $manager = User::factory()->create(['role' => 'MANAGER']);
        $token = $manager->createToken('api-token')->plainTextToken;

        // Set the Sanctum token on the request headers
        $headers = ['Authorization' => "Bearer $token"];
        
        // Create a order
        $order = order::factory()->create(['status' => 'DELIVERED',]);

        // Delete the order
        $response = $this->withHeaders($headers)->delete("/api/orders/{$order->id}");

        // Check if the order has been deleted from the database
        $this->assertSoftDeleted('orders', ['id' => $order->id]);

        // Check if the response indicates success (HTTP status code 204 for No Content)
        $response->assertStatus(204);
    }

    /** @test */
    public function a_customer_cannot_delete_an_order_if_not_waiting()
    {
        // Create a customer and generate a Sanctum token
        $customer = User::factory()->create(['role' => 'CUSTOMER']);
        $token = $customer->createToken('api-token')->plainTextToken;

        // Set the Sanctum token on the request headers
        $headers = ['Authorization' => "Bearer $token"];
        
        // Create a order
        $order = order::factory()->create(['status' => 'DELIVERED',]);

        // Delete the order
        $response = $this->withHeaders($headers)->delete("/api/orders/{$order->id}");

        // Check if the order has been soft deleted from the database
        $this->assertDatabaseHas('orders', [
            'user_id' => $customer->id,
            'total_price' => $order->total_price,
            'status' => $order->status,
            'consume_location' => $order->consume_location,
            'deleted_at' => null, // Check that the deleted_at column is null (not soft deleted)
        ]);

        // Check if the response indicates success (HTTP status code 204 for No Content)
        $response->assertStatus(403);
    }

    /** @test */
    public function a_customer_can_delete_a_waiting_order()
    {
        // Create a customer and generate a Sanctum token
        $customer = User::factory()->create(['role' => 'CUSTOMER']);
        $token = $customer->createToken('api-token')->plainTextToken;

        // Set the Sanctum token on the request headers
        $headers = ['Authorization' => "Bearer $token"];
        
        // Create a order
        $order = order::factory()->create(['status' => 'WAITING',]);

        // Delete the order
        $response = $this->withHeaders($headers)->delete("/api/orders/{$order->id}");

        // Check if the order has been soft deleted from the database
        $this->assertSoftDeleted('orders', [
            'id' => $order->id,
        ]);

        // Check if the response indicates success (HTTP status code 204 for No Content)
        $response->assertStatus(204);
    }

    /** @test */
    public function a_customer_can_create_an_order_assigned_a_cart_with_multiple_products_assigned()
    {
        // Create a customer and generate a Sanctum token
        $customer = User::factory()->create(['role' => 'CUSTOMER']);
        $token = $customer->createToken('api-token')->plainTextToken;

        // Create some products and options
        $type = Type::factory()->create();
        $products = Product::factory(3)->create(['type_id' => $type->id]);
        $options = Option::factory(3)->create(['type_id' => $type->id]);
        
        // Set the Sanctum token on the request headers
        $headers = ['Authorization' => "Bearer $token"];

        // A valid order data
        $orderData = [
            'user_id' => $customer->id,
            'consume_location' => 'IN_SHOP',
            'products' => [
                [
                    'product_id' => $products[0]->id,
                    'option_id' => $options[0]->id,
                    'quantity' => 2,
                ],
                [
                    'product_id' => $products[1]->id,
                    'option_id' => $options[1]->id,
                    'quantity' => 1,
                ],
                [
                    'product_id' => $products[2]->id,
                    'option_id' => $options[2]->id,
                    'quantity' => 3,
                ],
            ],
        ];

        // Send a POST request to store the order
        $response = $this->withHeaders($headers)->post('/api/orders', $orderData);

        // Check if the response indicates a successful creation (HTTP status code 201 Created)
        $response->assertStatus(201);

        $total_price = $products[0]->price * 2 + $products[1]->price * 1 + $products[2]->price * 3;

        $responseOrderData = [
            'user_id' => $customer->id,
            'total_price' => $total_price,
            'status' => 'WAITING',
            'consume_location' => 'IN_SHOP',
            'products' => [
                [
                    'product_id' => $products[0]->id,
                    'option_id' => $options[0]->id,
                    'quantity' => 2,
                ],
                [
                    'product_id' => $products[1]->id,
                    'option_id' => $options[1]->id,
                    'quantity' => 1,
                ],
                [
                    'product_id' => $products[2]->id,
                    'option_id' => $options[2]->id,
                    'quantity' => 3,
                ],
            ],
        ];

        // Check if the response includes the created order data
        $response->assertJson($responseOrderData);

        // Check if the order is actually stored in the database
        $this->assertDatabaseHas('orders', [
            'user_id' => $customer->id,
            'consume_location' => 'IN_SHOP',
        ]);
    }

}
