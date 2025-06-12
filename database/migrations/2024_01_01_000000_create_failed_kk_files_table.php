<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('failed_kk_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rw_id');
            $table->string('batch_id');
            $table->string('filename');
            $table->text('raw_text')->nullable();
            $table->enum('failure_reason', ['not_kk', 'processing_error', 'no_anggota_data']);
            $table->text('error_message')->nullable();
            $table->json('n8n_response')->nullable();
            $table->boolean('manually_processed')->default(false);
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->foreign('rw_id')->references('id')->on('rw')->onDelete('cascade');
            $table->index(['rw_id', 'manually_processed']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('failed_kk_files');
    }
};
