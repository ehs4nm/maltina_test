<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // User_id is not nullable so we need a user before creating an order
        // Fetch a random user from your database
        $randomUser = User::inRandomOrder()->first();

        // Create a user if there are no users yet
        if(! $randomUser) $randomUser = User::factory()->create(['role' => 'CUSTOMER']);

        $status = ['WAITING','PREPARATION','READY','DELIVERED'];
        $consume_location = ['TAKE_AWAY', 'IN_SHOP'];

        return [
            'total_price' => round(fake()->numberBetween(0, 999999) /1000)*1000,
            'status' => $status[array_rand($status)], // Select a random item
            'consume_location' => $consume_location[array_rand($consume_location)], // Select a random item
            'user_id' => $randomUser->id,
        ];
    }
}
