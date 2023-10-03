<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;

class ProductTest extends TestCase
{
    use RefreshDatabase; // Reset the database for each test

    /** @test */
    public function it_can_return_all_products()
    {
        // Create a user
        $user = User::factory(['role' => 'CUSTOMER'])->create(); 
        $this->actingAs($user);

        // Create products
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();

        // Retrieve all products
        $response = $this->get('/products');

        // Check if the response includes the products data
        $response->assertStatus(200)
            ->assertJson([
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
            ])
            ->assertJsonCount(2); // Ensure that two products are returned in the response.
    }

    /** @test */
    public function it_can_store_a_new_product()
    {   
        // Create a user
        $user = User::factory(['role' => 'CUSTOMER'])->create(); 
        $this->actingAs($user);
        
        // A valid product data
        $productData = [
            'name' => 'Latte',
            'price' => 58000,
        ];

        // Send a POST request to store the product
        $response = $this->post('/products', $productData);

        // Check if the response indicates a successful creation (HTTP status code 201 Created)
        $response->assertStatus(201);

        // Check if the response includes the created product data
        $response->assertJson($productData);

        // Check if the product is actually stored in the database
        $this->assertDatabaseHas('products', $productData);
    }

    /** @test */
    public function it_can_read_a_product()
    {
        // Create a user
        $user = User::factory(['role' => 'CUSTOMER'])->create(); 
        $this->actingAs($user);

        // Create a product
        $product = Product::factory()->create();

        // Retrieve the product
        $response = $this->get("/products/{$product->slug}");

        // Check if the response includes the product data
        $response->assertStatus(200)
            ->assertJson([
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => $product->price,
            ]);
    }

    /** @test */
    public function it_can_update_a_product()
    {
        // Create a user
        $user = User::factory(['role' => 'CUSTOMER'])->create(); 
        $this->actingAs($user);

        // Create a product
        $product = Product::factory()->create();

        // Update the product
        $response = $this->put("/products/{$product->slug}", [
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
    public function it_can_delete_a_product()
    {
        // Create a user
        $user = User::factory(['role' => 'CUSTOMER'])->create(); 
        $this->actingAs($user);
        
        // Create a product
        $product = Product::factory()->create();

        // Delete the product
        $response = $this->delete("/products/{$product->slug}");

        // Check if the product has been deleted from the database
        $this->assertDatabaseMissing('products', ['id' => $product->id]);

        // Check if the response indicates success (HTTP status code 204 for No Content)
        $response->assertStatus(204);
    }
}
