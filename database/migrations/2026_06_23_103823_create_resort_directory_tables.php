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
    // 1. Guests Directory Table
    Schema::create('guests', function (Blueprint $table) {
        $table->id();
        $table->string('customer_code', 20)->unique();
        $table->string('prefix', 10)->nullable();
        $table->string('first_name', 100);
        $table->string('last_name', 100);
        $table->date('birthdate')->nullable();
        $table->string('nationality', 100)->nullable();
        $table->string('email', 150)->unique();
        $table->string('country_code', 10)->nullable();
        $table->string('phone', 20)->nullable();
        $table->enum('guest_type', ['First-Time', 'Returning'])->default('First-Time');
        $table->timestamps();
    });

    // 2. Reservations & Bookings Table
    Schema::create('bookings', function (Blueprint $table) {
        $table->id();
        $table->foreignId('guest_id')->constrained('guests')->onDelete('cascade');
        $table->enum('booking_type', ['overnight', 'daytime'])->default('overnight');
        $table->date('check_in');
        $table->date('check_out');
        $table->integer('num_nights')->default(0);
        $table->decimal('base_price', 10, 2)->default(6000.00);
        $table->decimal('grand_total', 10, 2);
        $table->enum('status', ['Confirmed', 'Complete', 'Cancelled'])->default('Confirmed');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resort_directory_tables');
    }
};
