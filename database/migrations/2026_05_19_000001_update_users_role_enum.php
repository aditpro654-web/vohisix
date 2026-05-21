<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY role ENUM('admin', 'siswa', 'wali_kelas', 'kakonsli') NOT NULL DEFAULT 'siswa'");

        if (! Schema::hasColumn('users', 'kelas_id')) {
            Schema::table('users', function ($table) {
                $table->string('kelas_id')->nullable()->after('role');
                $table->string('kelas_second')->nullable()->after('kelas_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY role ENUM('admin', 'siswa') NOT NULL DEFAULT 'siswa'");

        if (Schema::hasColumn('users', 'kelas_second')) {
            Schema::table('users', function ($table) {
                $table->dropColumn('kelas_second');
            });
        }

        if (Schema::hasColumn('users', 'kelas_id')) {
            Schema::table('users', function ($table) {
                $table->dropColumn('kelas_id');
            });
        }
    }
};
