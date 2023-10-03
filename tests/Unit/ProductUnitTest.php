<?php

namespace Tests\Unit;

use App\Models\Option;
use App\Models\Product;
use App\Models\Type;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ProductUnitTest extends TestCase
{
    use RefreshDatabase;

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
    public function it_has_correct_types_and_options()
    {
        // Create a type for the product
        $type = Type::factory()->create();
        
        // Create a product
        $product = Product::factory()->create(['type_id' => $type->id]);

        // Create options for the type
        $option1 = Option::factory()->create(['type_id' => $type->id]);
        $option2 = Option::factory()->create(['type_id' => $type->id]);

        // Refresh the product to load its relationships
        $product->refresh();

        // Assert that the product has the correct type
        $this->assertEquals($type->id, $product->type->id);

        // Assert that the product's type has the correct options
        $this->assertCount(2, $product->type->options);
        $this->assertTrue($product->type->options->contains($option1));
        $this->assertTrue($product->type->options->contains($option2));
    }
}
