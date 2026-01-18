<?php

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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('item_number')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('unit'); // pcs, kg, liter, dll
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('restrict');
            $table->decimal('unit_price', 12, 2);
            $table->integer('minimum_stock')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
