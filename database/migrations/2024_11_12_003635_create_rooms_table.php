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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('building_id')
                ->nullable()
                ->constrained('buildings');
            $table->string('name')
                ->nullable();
            $table->string('code')
                ->nullable()
                ->unique();
            $table->string('price')
                ->nullable();
                $table->string('status')
                ->nullable()
                ->comment('status peminjaman, value: Available, Booked');
            $table->text('description')
                ->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
