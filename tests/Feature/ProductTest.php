<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase; // Reset the database for each test

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
            // 'slug'=> '', // slug will be produced in product model boot method
            'price' => 58000,
        ]);

        // Check product attributes
        $this->assertEquals('Latte', $product->name);
        $this->assertEquals(Str::slug('Latte'), $product->slug);
        $this->assertEquals(58000, $product->price);
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
