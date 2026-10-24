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
        Schema::table('pemberkasan', function (Blueprint $table) {
            $table->foreignId('jadwal_pemberkasan_id')
                ->after('jadwal_ujian_id')
                ->constrained('jadwal_pemberkasan')
                ->onDelete('cascade')
                ->nullable(); 
            $table->integer('nomor_urut')->nullable()->after('jadwal_pemberkasan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pemberkasan', function (Blueprint $table) {
            $table->dropForeign(['jadwal_pemberkasan_id']);
            $table->dropColumn(['jadwal_pemberkasan_id', 'nomor_urut']);
        });
    }
};
