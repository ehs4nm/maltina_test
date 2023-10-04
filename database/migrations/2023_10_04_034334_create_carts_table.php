<?php

use App\Models\Cart;
use App\Models\Option;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Order::class)->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('cart_product', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Cart::class)->nullable()->onDelete('cascade')->constrained();
            $table->foreignIdFor(Product::class)->nullable()->onDelete('cascade')->constrained();
            $table->foreignIdFor(Option::class)->nullable()->constrained(); // store the selected option of each product in the cart
            $table->integer('quantity')->default(1)->nullable(); // store the quantity of each product in the cart
            $table->unsignedInteger('sum_price')->nullable(); // store the quantity * price of each product in the cart
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
        Schema::dropIfExists('cart_product');
    }
};
