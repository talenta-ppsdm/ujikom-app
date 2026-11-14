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
        Schema::table('jadwal_wawancara', function (Blueprint $table) {
            $table->foreignId('jadwal_ujian_id')->constrained('jadwal_ujian')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwal_wawancara', function (Blueprint $table) {
            $table->dropForeign(['jadwal_ujian_id']);
            $table->dropColumn('jadwal_ujian_id');
        });
    }
};
