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
        Schema::create('anggota', function (Blueprint $table) {
            $table->id('id_anggota');
            $table->foreignId('id_user')->references('id_user')->on('users')->onDelete('cascade');
            $table->string('no_urut', 50)->nullable();
            $table->string('id_card', 50)->nullable();
            $table->string('nama_anggota', 100);
            $table->string('nip_nipppk', 100);
            $table->string('status_dosen', 50)->nullable();
            $table->string('homebase_pt', 100)->nullable();
            $table->string('provinsi', 100)->nullable();
            $table->string('foto', 255)->nullable();
            $table->enum('status_anggota', ['pending', 'aktif', 'nonaktif'])->default('pending');
            $table->string('bukti_tf_pendaftaran', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anggota');
    }
};
