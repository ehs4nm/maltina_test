<?php

namespace Tests\Feature;

use App\Models\Option;
use App\Models\Product;
use App\Models\Type;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;

class ProductTest extends TestCase
{
    use RefreshDatabase; // Reset the database for each test

    /** @test */
    public function it_can_return_all_products()
    {
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
    public function it_can_return_all_products_with_relations()
    {
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

        // Retrieve all products
        $response = $this->get('/api/products');
        
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
    public function it_can_create_a_product()
    {
        // Create a product
        $product = Product::create([
            'name' => 'Latte',
            // 'slug'=> '', // slug will be produced in product model boot method
            'price' => 58000,
        ]);

        // Check if the product exists in the database
        $this->assertDatabaseHas('products', [
            'name' => 'Latte',
            'price' => 58000,
        ]);

        // Check product attributes
        $this->assertEquals('Latte', $product->name);
        $this->assertEquals(Str::slug('Latte'), $product->slug);
        $this->assertEquals(58000, $product->price);
    }

        /** @test */
        public function it_can_store_a_new_product()
        {   
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
