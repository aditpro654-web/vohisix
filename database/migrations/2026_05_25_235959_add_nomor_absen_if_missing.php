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

        // Add unique index if it doesn't exist
        try {
            Schema::table('siswas', function (Blueprint $table) {
                $table->unique('nomor_absen');
            });
        } catch (\Exception $e) {
            // Index already exists or other error, continue anyway
        }
    }

    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            try {
                $table->dropUnique('siswas_nomor_absen_unique');
            } catch (\Exception $e) {
                // Index doesn't exist, continue anyway
            }
            if (Schema::hasColumn('siswas', 'nomor_absen')) {
                $table->dropColumn('nomor_absen');
            }
        });
    }
};
