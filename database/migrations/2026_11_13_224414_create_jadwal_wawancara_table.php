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
        Schema::create('jadwal_wawancara', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_id')->constrained('peserta')->onDelete('cascade');
            $table->foreignId('penguji_id')->constrained('penguji')->onDelete('cascade');
            $table->date('tanggal');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->string('metode', 50);
            $table->string('lokasi_link', 255);
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_wawancara');
    }
};
