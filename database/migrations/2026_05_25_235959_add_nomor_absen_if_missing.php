<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add column if it doesn't exist yet. Safe to run multiple times.
        Schema::table('siswas', function (Blueprint $table) {
            if (!Schema::hasColumn('siswas', 'nomor_absen')) {
                $table->integer('nomor_absen')->nullable()->after('kelas');
            }
        });
    }

    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            if (Schema::hasColumn('siswas', 'nomor_absen')) {
                $table->dropColumn('nomor_absen');
            }
        });
    }
};
