<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pastikan tabel bookings sudah ada sebelum menambahkan kolom
        if (Schema::hasTable('bookings')) {
            Schema::table('bookings', function (Blueprint $table) {
                // Tambahkan kolom baru untuk member booking
                if (!Schema::hasColumn('bookings', 'is_member_booking')) {
                    $table->boolean('is_member_booking')->default(false);
                    $table->string('recurring_day')->nullable(); // Menyimpan hari (0-6, 0=Minggu)
                    $table->time('recurring_start_time')->nullable();
                    $table->time('recurring_end_time')->nullable();
                    $table->date('recurring_until')->nullable(); // Tanggal berakhir
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'is_member_booking',
                'recurring_day',
                'recurring_start_time',
                'recurring_end_time',
                'recurring_until'
            ]);
        });
    }
}; 