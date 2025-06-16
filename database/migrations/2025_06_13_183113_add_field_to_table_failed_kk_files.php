<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('failed_kk_files', function (Blueprint $table) {
            $table->string('original_filename')->nullable(); // Add this
            $table->string('file_path')->nullable(); // Add this for image path
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('failed_kk_files', function (Blueprint $table) {
            //
        });
    }
};
