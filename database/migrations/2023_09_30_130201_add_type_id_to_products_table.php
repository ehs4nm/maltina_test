<?php

use App\Models\Type;
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
        Schema::table('products', function (Blueprint $table) {
            // Add the foreign key column
            $table->foreignIdFor(Type::class)->after('name')->nullable();
            // Define the foreign key constraint
            $table->foreign('type_id')->references('id')->on('types')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Remove the foreign key column
            $table->dropForeignIdFor(Type::class);

            // Drop the column
            $table->dropColumn('type_id');
        });
    }
};
