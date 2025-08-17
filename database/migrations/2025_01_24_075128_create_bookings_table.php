<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('court_id')->constrained('basket_courts')->onDelete('cascade'); // Tambahkan ini
            $table->foreignId('schedule_id')->nullable()->constrained('schedules')->onDelete('set null');
            $table->integer('duration')->default(1);
            $table->decimal('total_price', 10, 2);
            $table->decimal('down_payment', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'waiting_payment', 'paid', 'completed', 'cancelled'])->default('pending');
            $table->boolean('terms_accepted')->default(false);
            $table->timestamp('terms_accepted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
