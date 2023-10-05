<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\Option;
use App\Models\Order;
use App\Models\Product;
use App\Models\Type;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Order::factory(3)
            ->has(Cart::factory()
                ->hasAttached(Product::factory(3)
                    ->for(Type::factory()
                        ->has($options = Option::factory(3))), ['option_id' => $options->create()[0]->id, 'sum_price'=>100000])
                )->create();
    }
}
