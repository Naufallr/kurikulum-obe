<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('mst_mk')) {
            return;
        }

        $driver = DB::getDriverName();

        // Catatan:
        // - Untuk Postgres, nama constraint PK bisa berbeda-beda, jadi kita cari dari pg_constraint.
        // - Untuk MySQL/MariaDB, DROP PRIMARY KEY tidak perlu nama constraint.
        if ($driver === 'pgsql') {
            // Pastikan kolom PK tidak null
            DB::statement('ALTER TABLE mst_mk ALTER COLUMN nama_mk SET NOT NULL');

            // Drop primary key constraint apapun (jika ada)
            DB::unprepared(<<<'SQL'
DO $$
DECLARE
    pk_name text;
BEGIN
    SELECT conname INTO pk_name
    FROM pg_constraint
    WHERE contype = 'p'
      AND conrelid = 'mst_mk'::regclass
    LIMIT 1;

    IF pk_name IS NOT NULL THEN
        EXECUTE format('ALTER TABLE mst_mk DROP CONSTRAINT %I', pk_name);
    END IF;
END $$;
SQL);

            DB::statement('ALTER TABLE mst_mk ADD PRIMARY KEY (nama_mk)');
        } elseif ($driver === 'mysql' || $driver === 'mariadb') {
            // Pastikan kolom PK tidak null (MySQL/MariaDB)
            DB::statement('ALTER TABLE mst_mk MODIFY nama_mk VARCHAR(255) NOT NULL');

            try {
                DB::statement('ALTER TABLE mst_mk DROP PRIMARY KEY');
            } catch (\Throwable $e) {
                // Abaikan jika belum ada primary key
            }

            DB::statement('ALTER TABLE mst_mk ADD PRIMARY KEY (nama_mk)');
        }
        // sqlite/sqlsrv: skip (ubah PK butuh rebuild table)
    }

    public function down(): void
    {
        // Tidak di-rollback otomatis karena perubahan PK bisa berdampak ke relasi.
    }
};

