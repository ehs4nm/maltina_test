<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\Type;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TypeUnitTest extends TestCase
{
    use RefreshDatabase; // Reset the database for each test

    /** @test */
    public function it_can_create_a_type()
    {
        // Create a type
        $type = Type::create([
            'name' => 'Size',
        ]);

        // Check if the type exists in the database
        $this->assertDatabaseHas('types', [
            'name' => 'Size',
        ]);

        // Check type attributes
        $this->assertEquals('Size', $type->name);
    }

        /** @test */
        public function it_can_associate_type_with_product()
        {
            // Create a product
            $product = Product::factory()->create();

            // Create a type
            $type = Type::factory()->create();
    
            // Associate the product with the Type
            $product->type()->associate($type);
            $product->save();

            // Retrieve the saved options
            $savedType = $product->type->fresh();
    
            // Assert that the associated type's ID matches the original type's ID
            $this->assertEquals($type->id, $product->type->id);
            $this->assertEquals($type->name, $product->type->name);
        }
    //  CRUD operations have not been implemented as they are not mentioned in the challenge document.
}
