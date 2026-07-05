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
    // 3. Amenities Catalog Table
    Schema::create('amenities', function (Blueprint $table) {
        $table->id();
        $table->string('name', 100);
        $table->text('description')->nullable();
        $table->decimal('price', 10, 2);
        $table->enum('price_type', ['flat', 'per_night'])->default('flat');
        $table->timestamps();
    });

    // 4. Booking Amenities Pivot Junction Table
    Schema::create('booking_amenities', function (Blueprint $table) {
        $table->id();
        $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
        $table->foreignId('amenity_id')->constrained('amenities')->onDelete('cascade');
        $table->integer('quantity')->default(1);
        $table->decimal('captured_price', 10, 2);
        $table->timestamps();
    });

    // 5. Payments Ledger Table
    Schema::create('payments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
        $table->decimal('amount', 10, 2);
        $table->date('payment_date');
        $table->enum('status', ['Paid', 'Refunded', 'Pending'])->default('Paid');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('amenities_and_pivot_tables');
    }
};
