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
        Schema::table('jadwal_ujian', function (Blueprint $table) {
            $table->dateTime('realtime_mulai')->nullable(); 
            $table->dateTime('realtime_selesai')->nullable();
            $table->dateTime('target_selesai')->nullable(); 
    
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwal_ujian', function (Blueprint $table) {
            $table->dropColumn(['realtime_mulai', 'realtime_selesai', 'target_selesai']);
        });
    }
};
