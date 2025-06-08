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
        Schema::create('kk_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kk_id')->constrained('kk')->onDelete('cascade');
            $table->string('img_name')->nullable();
            $table->string('nama_kepala_keluarga')->nullable();
            $table->text('alamat')->nullable();
            $table->string('rt')->nullable();
            $table->string('rw')->nullable();
            $table->string('kode_pos')->nullable();
            $table->string('desa_kelurahan')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kabupaten_kota')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('no_kk')->nullable();
            $table->date('kk_disahkan_tanggal')->nullable();
            $table->string('nama_lengkap')->nullable();
            $table->string('nik')->nullable();
            $table->enum('jenis_kelamin', ['LAKI-LAKI', 'PEREMPUAN'])->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('agama')->nullable();
            $table->string('pendidikan')->nullable();
            $table->string('jenis_pekerjaan')->nullable();
            $table->string('golongan_darah')->nullable();
            $table->string('status_perkawinan')->nullable();
            $table->string('status_hubungan_dalam_keluarga')->nullable();
            $table->string('kewarganegaraan')->nullable();
            $table->string('no_paspor')->nullable();
            $table->string('no_kitap')->nullable();
            $table->string('ayah')->nullable();
            $table->string('ibu')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kk_members');
    }
};
