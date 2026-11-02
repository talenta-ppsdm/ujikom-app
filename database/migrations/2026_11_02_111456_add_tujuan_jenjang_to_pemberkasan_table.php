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
            $table->string('tujuan_jenjang')->nullable()->after('tujuan_ujian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pemberkasan', function (Blueprint $table) {
            $table->dropColumn('tujuan_jenjang');
        });
    }
};
