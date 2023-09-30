<?php

use App\Models\User;
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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('total_price')->nullable();
            $table->enum('status', ['WAITING','PREPARATION','READY','DELIVERED'])->default('WAITING')->nullable();
            $table->enum('consume_location', ['TAKE_AWAY', 'IN_SHOP'])->default('TAKE_AWAY')->nullable();
            $table->foreignIdFor(User::class)->constrained(); // user_id is not nullable as we don't want any orders without a specific user assigned to it
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
