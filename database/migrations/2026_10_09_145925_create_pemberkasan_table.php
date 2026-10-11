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
        Schema::create('pemberkasan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_id')->constrained('peserta')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('jadwal_ujian_id')->constrained('jadwal_ujian')->onDelete('cascade')->nullable(); //TODO Simpan data jadwal ujian maka masukin id jadwal disini
            $table->string('status',20);
            $table->text('catatan',255)->nullable();

            // Pemberkasan awal
            $table->string('file_sk_golongan')->nullable();
            $table->string('file_sk_jabatan')->nullable();

            // Pemberkasan ujian
            $table->string('file_portofolio')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemberkasan');
    }
};
