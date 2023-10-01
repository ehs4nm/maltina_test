<?php

namespace Tests\Unit;

use App\Models\Option;
use App\Models\Type;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OptionUnitTest extends TestCase
{
    use RefreshDatabase; // Reset the database for each test

    /** @test */
    public function it_can_create_an_option()
    {
        // Create a option
        $option = Option::create([
            'name' => 'small',
        ]);

        // Check if the option exists in the database
        $this->assertDatabaseHas('options', [
            'name' => 'small',
        ]);

        // Check option attributes
        $this->assertEquals('small', $option->name);
    }

    /** @test */
    public function it_can_associate_options_with_type()
    {
        // Create a type
        $type = Type::factory()->create();

        // Create 3 options
        $options = Option::factory(3)->create();

        // Associate the Options with the Type
        $type->options()->saveMany($options);

        // Retrieve the saved options
        $savedOptions = $type->options;

        // Assert that the number of saved options is 3
        $this->assertCount(3, $savedOptions);
    }

    //  CRUD operations have not been implemented as they are not mentioned in the challenge document.
}
