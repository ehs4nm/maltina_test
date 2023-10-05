<?php

namespace Tests\Feature;

use App\Models\Option;
use App\Models\Product;
use App\Models\Type;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase; // Reset the database for each test
   
    /** @test */
    public function it_can_return_all_products_with_relations()
    {
        // Create a user and generate a Sanctum token
        $user = User::factory()->create(['role' => 'CUSTOMER']);
        $token = $user->createToken('api-token')->plainTextToken;

        // Set the Sanctum token on the request headers
        $headers = ['Authorization' => "Bearer $token"];

        // Create some types
        $type1 = Type::factory()->create();
        $type2 = Type::factory()->create();

        // Create some products assocated with types
        $product1 = Product::factory()->create(['type_id' => $type1->id]);
        $product2 = Product::factory()->create(['type_id' => $type2->id]);
        $productWithoutType = Product::factory()->create();

        // Create some options assocated with types
        $options1 = Option::factory(3)->create(['type_id' => $type1->id]);
        $options2 = Option::factory(2)->create(['type_id' => $type2->id]);


        // Retrieve all products with authentication headers
        $response = $this->withHeaders($headers)->get('/api/products');
        
        // Expected JSON structure for response
        $expectedResponseJsonStructure = [
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'price',
                ],
            ],
        ];

        // Check if the response is OK and has a correct JSON structure
        $response->assertStatus(200)
            ->assertJsonStructure($expectedResponseJsonStructure) //Ensure the response has a correct structure
            ->assertJsonCount(3, 'data'); // Ensure that three products are returned in the response.

        // Convert the JSON response to an array
        $responseData = $response->json(['data']);

        // Check if the response includes the products data (types and options)
        foreach ($responseData as $productData) {
            $product = Product::find($productData['id']);

            if ($product && $product->type) {
                // If the product does not have a type, assert its structure and values
                $this->assertEquals($product->id, $productData['id']);
                $this->assertEquals($product->name, $productData['name']);
            }

            if ($product && $product->type) {
                // If the product has a type, assert its structure and options
                $this->assertArrayHasKey('type', $productData);
                $this->assertEquals($product->type->id, $productData['type']['id']);
                $this->assertEquals($product->type->name, $productData['type']['name']);
                $this->assertCount($product->type->options->count(), $productData['type']['options']);
            }
        }
    }

    /** @test */
    public function a_manager_can_return_all_products()
    {
        // Create a manager
        $manager = User::factory(['role' => 'MANAGER'])->create(); 
        $this->actingAs($manager);

        // Create products
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();

        // Retrieve all products
        $response = $this->get('/api/products');

        // Check if the response includes the products data
        $response->assertStatus(200)
            ->assertJson(['data' => [
                [
                    'id' => $product1->id,
                    'name' => $product1->name,
                    'price' => $product1->price,
                ],
                [
                    'id' => $product2->id,
                    'name' => $product2->name,
                    'price' => $product2->price,
                ],]
            ])
            ->assertJsonCount(2, 'data'); // Ensure that two products are returned in the response.
    }

    /** @test */
    public function a_customer_can_return_all_products()
    {
        // Create a customer
        $customer = User::factory(['role' => 'CUSTOMER'])->create(); 
        $this->actingAs($customer);

        // Create products
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();

        // Retrieve all products
        $response = $this->get('/api/products');

        // Check if the response includes the products data
        $response->assertStatus(200)
            ->assertJson(['data' => [
                [
                    'id' => $product1->id,
                    'name' => $product1->name,
                    'price' => $product1->price,
                ],
                [
                    'id' => $product2->id,
                    'name' => $product2->name,
                    'price' => $product2->price,
                ],
            ]])
            ->assertJsonCount(2, 'data'); // Ensure that two products are returned in the response.
    }

    /** @test */
    public function a_manager_can_store_a_new_product()
    {   
        // Create a manager user
        $manager = User::factory(['role' => 'MANAGER'])->create(); 
        $this->actingAs($manager);
        
        // A valid product data
        $productData = [
            'name' => 'Latte',
            'price' => 58000,
        ];

        // Send a POST request to store the product
        $response = $this->post('/api/products', $productData);

        // Check if the response indicates a successful creation (HTTP status code 201 Created)
        $response->assertStatus(201);

        // Check if the response includes the created product data
        $response->assertJson($productData);

        // Check if the product is actually stored in the database
        $this->assertDatabaseHas('products', $productData);
    }

    /** @test */
    public function a_customer_cannot_store_a_new_product()
    {   
        // Create a customer user
        $customer = User::factory(['role' => 'CUSTOMER'])->create(); 
        $this->actingAs($customer);
        
        // A valid product data
        $productData = [
            'name' => 'Latte',
            'price' => 58000,
        ];

        // Send a POST request to store the product
        $response = $this->post('/api/products', $productData);

        // Check if the response indicates an unauthorized action (HTTP status code 403 Forbidden)
        $response->assertStatus(403);

        // Check if the product is not stored in the database
        $this->assertDatabaseMissing('products', $productData);
    }

    /** @test */
    public function a_manager_can_read_a_product()
    {
        // Create a user
        $user = User::factory(['role' => 'MANAGER'])->create(); 
        $this->actingAs($user);

        // Create a product
        $product = Product::factory()->create();

        // Retrieve the product
        $response = $this->get("/api/products/{$product->slug}");

        // Check if the response includes the product data
        $response->assertStatus(200)
            ->assertJson([
                'name' => $product->name,
                'price' => $product->price,
            ]);
    }

    /** @test */
    public function a_customer_can_read_a_product()
    {
        // Create a customer
        $customer = User::factory(['role' => 'CUSTOMER'])->create(); 
        $this->actingAs($customer);

        // Create a product
        $product = Product::factory()->create();

        // Retrieve the product
        $response = $this->get("/api/products/{$product->slug}");

        // Check if the response includes the product data
        $response->assertStatus(200)
            ->assertJson([
                'name' => $product->name,
                'price' => $product->price,
            ]);
    }

    /** @test */
    public function a_manager_can_update_a_product()
    {
        // Create a manager
        $manager = User::factory(['role' => 'MANAGER'])->create(); 
        $this->actingAs($manager);

        // Create a product
        $product = Product::factory()->create();

        // Update the product
        $response = $this->put("/api/products/{$product->slug}", [
            'name' => 'Updated Latte',
            'slug'=> Str::slug('Updated Latte'),
            'price' => 58000,
        ]);

        // Check if the product attributes have been updated in the database
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Latte',
            'slug'=> Str::slug('Updated Latte'),
            'price' => 58000,
        ]);

        // Check if the response indicates success (HTTP status code 200 for OK)
        $response->assertStatus(200);
    }

    /** @test */
    public function a_customer_cannot_update_a_product()
    {
        // Create a customer
        $customer = User::factory(['role' => 'CUSTOMER'])->create(); 
        $this->actingAs($customer);

        // Create a product
        $product = Product::factory()->create();

        // Update the product
        $response = $this->put("/api/products/{$product->slug}", [
            'name' => 'Updated Latte',
            'slug'=> Str::slug('Updated Latte'),
            'price' => 58000,
        ]);

        // Check if the product attributes have not been updated in the database
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => $product->name,
            'slug'=> $product->slug,
            'price' => $product->price,
        ]);

        // Check if the response indicates success (HTTP status code 200 for OK)
        $response->assertStatus(403);
    }
    
    /** @test */
    public function a_manager_can_delete_a_product()
    {
        // Create a manager
        $manager = User::factory(['role' => 'MANAGER'])->create(); 
        $this->actingAs($manager);
        
        // Create a product
        $product = Product::factory()->create();

        // Delete the product
        $response = $this->delete("/api/products/{$product->slug}");

        // Check if the product has been deleted from the database
        $this->assertDatabaseMissing('products', ['id' => $product->id]);

        // Check if the response indicates success (HTTP status code 204 for No Content)
        $response->assertStatus(204);
    }

    /** @test */
    public function a_customer_cannot_delete_a_product()
    {
        // Create a customer
        $customer = User::factory(['role' => 'CUSTOMER'])->create(); 
        $this->actingAs($customer);
        
        // Create a product
        $product = Product::factory()->create();

        // Delete the product
        $response = $this->delete("/api/products/{$product->slug}");

        // Check if the product has been deleted from the database
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'price' => $product->price,
        ]);

        // Check if the response indicates success (HTTP status code 204 for No Content)
        $response->assertStatus(403);
    }
}
