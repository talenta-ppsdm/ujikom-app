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
            $table->foreignId('jadwal_ujian_id')->constrained('jadwal_ujian')->onDelete('cascade');
            $table->string('tujuan_ujian', 255);
            $table->string('file_sk_golongan')->nullable();
            $table->string('file_sk_jabatan')->nullable();
            $table->string('file_portofolio')->nullable();
            $table->string('file_praasesmen')->nullable();
            $table->string('file_paparan')->nullable();
            $table->dateTime('tanggal_daftar');
            
            $table->string('status', 50);
            $table->foreignId('verified_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('cascade');
            $table->timestamp('verified_at')->nullable();
            $table->text('catatan')->nullable();
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
