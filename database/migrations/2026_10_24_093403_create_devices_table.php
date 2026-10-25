<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('device_code')->unique();
            $table->string('name');
            $table->string('mcu')->default('ESP32-S3 CAM');
            $table->string('gps_module')->default('NEO-6M');
            $table->string('status')->default('Active');
            $table->timestamp('last_seen_at')->nullable();
            $table->string('gps_status')->default('Unknown');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('firmware_version')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
