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
        Schema::create('jadwal_ujian', function (Blueprint $table) {
            $table->id();
            $table->string('gelombang', 255);
            $table->string('tahun', 10);
            $table->integer('kuota');
            $table->string('status');
            $table->date('periode_awal');
            $table->date('periode_akhir');
            $table->date('periode_pemberkasan_awal')->nullable();
            $table->date('periode_pemberkasan_akhir')->nullable();
            $table->date('periode_portofolio_awal')->nullable();
            $table->date('periode_portofolio_akhir')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_ujian');
    }
};
