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
        Schema::create('borrowed_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->date('transaction_date')
                ->nullable()
                ->comment('tanggal transaksi');
            $table->string('description')
                ->nullable();
            $table->date('borrowed_at')
                ->nullable()
                ->comment('tanggal peminjaman');
            $table->string('status')
                ->nullable()
                ->comment('status , value: Approved, Rejected');
            $table->date('returned_at')
                ->nullable()
                ->comment('tanggal pengembalian');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrowed_items');
    }
};
