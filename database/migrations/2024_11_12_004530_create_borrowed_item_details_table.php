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
        Schema::create('borrowed_item_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('borrowed_item_id')
                ->nullable()
                ->constrained('borrowed_items')
                ->onDelete('cascade');;
            $table->foreignId('item_id')
                ->nullable()
                ->constrained('items');
            $table->string('borrowed_condition')
                ->nullable();
            $table->string('returned_condition')
                ->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrowed_item_details');
    }
};
