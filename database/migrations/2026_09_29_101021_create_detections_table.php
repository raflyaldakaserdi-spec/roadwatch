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
        Schema::create('detections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); // Menunjuk ke akun pekerja/masyarakat
            $table->string('detection_code');
            $table->string('image');
            $table->string('location');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->date('detection_date');
            $table->string('detection_day');
            $table->time('detection_time');
            $table->enum('status', ['Detected', 'In Progress', 'Repaired'])->default('Detected');
            $table->enum('severity', ['Low', 'Medium', 'High'])->default('Medium');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detections');
    }
};