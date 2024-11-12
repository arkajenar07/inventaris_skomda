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
        Schema::create('room_booking_externals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_booking_id')
                ->nullable()
                ->constrained('room_bookings')
                ->onDelete('cascade');
            $table->string('customer_name');
            $table->string('customer_photo');
            $table->string('customer_phone');
            $table->string('customer_company');
            $table->string('payment_status')
                ->nullable()
                ->comment('status pembayaran, value: Pending, Done');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_booking_externals');
    }
};
