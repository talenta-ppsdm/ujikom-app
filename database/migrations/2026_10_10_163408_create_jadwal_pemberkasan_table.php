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
        Schema::create('jadwal_pemberkasan', function (Blueprint $table) {
            $table->id();
            $table->string('gelombang',20);
            $table->year('tahun');
            $table->string('status',50);
            $table->integer('kuota');
            $table->date('periode_awal');
            $table->date('periode_akhir');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_pemberkasan');
    }
};
