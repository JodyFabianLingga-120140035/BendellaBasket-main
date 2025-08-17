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
        Schema::create('basket_courts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('location');
            $table->text('description')->nullable();
            $table->decimal('price_per_hour', 10, 2);
            $table->boolean('is_available')->default(true);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('photo')->nullable();
            $table->json('holiday_dates')->nullable();
            $table->time('holiday_start_time')->nullable();
            $table->time('holiday_end_time')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('basket_courts');
    }
};