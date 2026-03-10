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
        if (!Schema::hasTable('mst_mk')) {
            return;
        }

        if (!Schema::hasColumn('mst_mk', 'semester')) {
            Schema::table('mst_mk', function (Blueprint $table) {
                $table->unsignedTinyInteger('semester')->nullable();
            });
        }

        if (DB::getDriverName() === 'pgsql') {
            DB::statement(<<<'SQL'
ALTER TABLE mst_mk
ALTER COLUMN semester TYPE SMALLINT
USING (
    CASE
        WHEN lower(trim(semester::text)) = 'ganjil' THEN 1
        WHEN lower(trim(semester::text)) = 'genap' THEN 2
        WHEN trim(semester::text) ~ '^[1-8]$' THEN trim(semester::text)::smallint
        ELSE NULL
    END
)
SQL);

            DB::statement('ALTER TABLE mst_mk DROP CONSTRAINT IF EXISTS mst_mk_semester_check');
            DB::statement('ALTER TABLE mst_mk ADD CONSTRAINT mst_mk_semester_check CHECK (semester BETWEEN 1 AND 8)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('mst_mk') || !Schema::hasColumn('mst_mk', 'semester')) {
            return;
        }

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE mst_mk DROP CONSTRAINT IF EXISTS mst_mk_semester_check');
            DB::statement('ALTER TABLE mst_mk ALTER COLUMN semester TYPE VARCHAR(20) USING semester::text');
        }
    }
};