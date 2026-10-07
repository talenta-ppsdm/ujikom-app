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
        Schema::table('peserta', function (Blueprint $table) {
            $table->string('golongan')->nullable()->change();
            $table->string('jabatan')->nullable()->change();
            $table->string('unit')->nullable()->change();
            $table->string('instansi')->nullable()->change();
            $table->string('telepon')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peserta', function (Blueprint $table) {
            $table->string('golongan')->nullable(false)->change();
            $table->string('jabatan')->nullable(false)->change();
            $table->string('unit')->nullable(false)->change();
            $table->string('instansi')->nullable(false)->change();
            $table->string('telepon')->nullable(false)->change();
        });
    }
};
