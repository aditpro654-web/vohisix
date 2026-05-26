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
        Schema::table('dudis', function (Blueprint $table) {
            if (!Schema::hasColumn('dudis', 'status')) {
                $table->string('status')->default('active')->after('logo');
            }
        });

        Schema::table('berkas', function (Blueprint $table) {
            if (!Schema::hasColumn('berkas', 'ktp_kia_name')) {
                $table->string('ktp_kia_name')->nullable()->after('ktp_kia');
            }
            if (!Schema::hasColumn('berkas', 'surat_sehat_name')) {
                $table->string('surat_sehat_name')->nullable()->after('surat_sehat');
            }
            if (!Schema::hasColumn('berkas', 'kartu_bpjs_name')) {
                $table->string('kartu_bpjs_name')->nullable()->after('kartu_bpjs');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dudis', function (Blueprint $table) {
            if (Schema::hasColumn('dudis', 'status')) {
                $table->dropColumn('status');
            }
        });

        Schema::table('berkas', function (Blueprint $table) {
            if (Schema::hasColumn('berkas', 'ktp_kia_name')) {
                $table->dropColumn('ktp_kia_name');
            }
            if (Schema::hasColumn('berkas', 'surat_sehat_name')) {
                $table->dropColumn('surat_sehat_name');
            }
            if (Schema::hasColumn('berkas', 'kartu_bpjs_name')) {
                $table->dropColumn('kartu_bpjs_name');
            }
        });
    }
};
