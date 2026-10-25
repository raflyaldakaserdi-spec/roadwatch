<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_assignments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('detection_id')
                ->constrained('detections')
                ->restrictOnDelete();

            $table->foreignId('worker_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('assigned_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('priority', 20)->default('Medium');
            $table->string('status', 30)->default('Assigned');

            $table->text('instructions')->nullable();
            $table->text('worker_notes')->nullable();
            $table->string('completion_photo')->nullable();

            $table->date('due_date')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            $table->unique('detection_id');
            $table->index(['worker_id', 'status']);
            $table->index(['status', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_assignments');
    }
};
