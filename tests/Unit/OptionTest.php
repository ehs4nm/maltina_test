<?php

namespace Tests\Unit;

use App\Models\Option;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OptionTest extends TestCase
{
    use RefreshDatabase; // Reset the database for each test

    /** @test */
    public function it_can_create_an_option()
    {
        // Create a type
        $option = Option::create([
            'name' => 'small',
        ]);

        // Check if the type exists in the database
        $this->assertDatabaseHas('options', [
            'name' => 'small',
        ]);

        // Check type attributes
        $this->assertEquals('small', $option->name);
    }
    //  CRUD operations have not been implemented as they are not mentioned in the challenge document.
}
