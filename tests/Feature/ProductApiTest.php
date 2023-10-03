<?php

namespace Tests\Feature;

use App\Models\Option;
use App\Models\Product;
use App\Models\Type;
use App\Models\User;
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
}
