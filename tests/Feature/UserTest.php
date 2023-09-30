<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase; // Reset the database for each test

    /** @test */
    public function it_can_create_a_user()
    {
        // Create user data
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
            'role' => 'MANAGER',
        ];

        // Create a user
        $user = User::create($userData);

        // Check if the user exists in the database
        $this->assertDatabaseHas('users', ['email' => 'john@example.com']);

        // Check user attributes
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertEquals('MANAGER', $user->role);
    }

    /** @test */
    public function it_can_update_a_user()
    {
        // Create a user
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
            'role' => 'MANAGER',
        ]);

        // Update user data
        $user->update(['name' => 'Ali']);
        $user->update(['role' => 'CUSTOMER']);

        // Check if the user's name has been updated
        // $user->fresh(), retrieves the latest data for the $user model instance from the database. 
        $this->assertEquals('Ali', $user->fresh()->name);
        $this->assertEquals('CUSTOMER', $user->fresh()->role);
    }

    /** @test */
    public function it_can_delete_a_user()
    {
        // Create a user
        $user = User::create([
            'name' => 'Bob',
            'email' => 'bob@example.com',
            'password' => bcrypt('password'),
            'role' => 'MANAGER',
        ]);

        // Delete the user
        $user->delete();

        // Check if the user has been deleted from the database
        $this->assertDatabaseMissing('users', ['email' => 'bob@example.com']);
    }
}
