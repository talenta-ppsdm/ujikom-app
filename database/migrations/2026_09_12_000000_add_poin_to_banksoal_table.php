<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('banksoal', function (Blueprint $table) {
            $table->unsignedInteger('poin')->default(2)->after('level');
        });
    }

    public function down(): void
    {
        Schema::table('banksoal', function (Blueprint $table) {
            $table->dropColumn('poin');
        });
    }
};
