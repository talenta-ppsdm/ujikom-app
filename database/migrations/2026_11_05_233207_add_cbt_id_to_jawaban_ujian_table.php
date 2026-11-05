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
        Schema::table('jawaban_ujian', function (Blueprint $table) {
            $table->foreignId('cbt_id')->constrained('cbt')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jawaban_ujian', function (Blueprint $table) {
            $table->dropForeign(['cbt_id']);
            $table->dropColumn('cbt_id');
        });
    }
};
