<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('tbl_bobot_penilaian')) {
            return;
        }

        if (!Schema::hasColumn('tbl_bobot_penilaian', 'id')) {
            Schema::table('tbl_bobot_penilaian', function (Blueprint $table) {
                $table->bigIncrements('id');
            });

            // Backfill for existing rows (PostgreSQL).
            DB::statement("UPDATE tbl_bobot_penilaian SET id = nextval(pg_get_serial_sequence('tbl_bobot_penilaian','id')) WHERE id IS NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('tbl_bobot_penilaian') && Schema::hasColumn('tbl_bobot_penilaian', 'id')) {
            Schema::table('tbl_bobot_penilaian', function (Blueprint $table) {
                $table->dropColumn('id');
            });
        }
    }
};
