<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_assignment_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('work_assignment_id')
                ->constrained('work_assignments')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('action', 50);
            $table->string('old_status', 50)->nullable();
            $table->string('new_status', 50);
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index([
                'work_assignment_id',
                'created_at',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_assignment_histories');
    }
};
