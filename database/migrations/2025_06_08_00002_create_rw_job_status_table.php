<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rw_job_status', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rw_id')->constrained('rw')->onDelete('cascade');
            $table->string('batch_id')->unique();
            $table->enum('status', ['processing', 'completed', 'failed'])->default('processing');
            $table->integer('total_jobs')->default(0);
            $table->integer('processed_jobs')->default(0);
            $table->integer('failed_jobs')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rw_job_status');
    }
};
