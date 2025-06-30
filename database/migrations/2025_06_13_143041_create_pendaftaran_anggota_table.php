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
        Schema::create('pendaftaran_anggota', function (Blueprint $table) {
            $table->id('id_pendaftaran');
            $table->foreignId('id_anggota')->references('id_anggota')->on('anggota')->onDelete('cascade');
            $table->string('bukti_transaksi', 255)->nullable();
            $table->string('status_pendaftaran', 50)->default('pending'); // pending, approved, rejected
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendaftaran_anggota');
    }
};
