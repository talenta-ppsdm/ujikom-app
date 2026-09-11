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
        Schema::create('banksoal', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 50)->unique();
            $table->string('soal', 255);
            $table->string('kategori', 100);
            $table->string('level', 50);
            $table->string('jawaban_a', 255);
            $table->string('jawaban_b', 255);
            $table->string('jawaban_c', 255);
            $table->string('jawaban_d', 255);
            $table->string('jawaban_e', 255);
            $table->string('kunci',50);
            $table->string('pembahasan',255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banksoal');
    }
};
