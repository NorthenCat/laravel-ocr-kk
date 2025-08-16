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
        Schema::table('rw', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['desa_id']);

            // Change the column to a regular unsignedBigInteger
            $table->unsignedBigInteger('desa_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rw', function (Blueprint $table) {
            // Re-add the foreign key constraint
            $table->foreign('desa_id')
                ->references('id')
                ->on('desa')
                ->onDelete('cascade');
        });
    }
};
