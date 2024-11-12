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
        Schema::create('room_booking_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_booking_id')
                ->nullable()
                ->constrained('room_bookings')
                ->onDelete('cascade');;
            $table->foreignId('room_id')
                ->nullable()
                ->constrained('rooms');
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
        Schema::dropIfExists('room_booking_details');
    }
};
