<?php

namespace Tests\Unit;

use App\Models\Option;
use App\Models\Product;
use App\Models\Type;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductUnitTest extends TestCase
{
    use RefreshDatabase;

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
