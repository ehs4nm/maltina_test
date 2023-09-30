<?php

namespace Tests\Feature;

use App\Models\Type;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TypeTest extends TestCase
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
    //  CRUD operations have not been implemented as they are not mentioned in the challenge document.
}
