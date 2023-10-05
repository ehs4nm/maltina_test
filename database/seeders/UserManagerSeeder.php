<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a user with the specified name and password
        $user = User::create([
            'name' => 'mohiti',
            'email' => 'mohiti.ehsan@gmail.com',
            'role' => 'MANAGER',
            'password' => bcrypt('120120120'),
        ]);

        $customers = User::factory(3)->create(['role' => 'CUSTOMER']);
    }
}
