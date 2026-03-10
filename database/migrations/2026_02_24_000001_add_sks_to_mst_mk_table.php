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
        if (!Schema::hasTable('mst_mk') || Schema::hasColumn('mst_mk', 'sks')) {
            return;
        }

        Schema::table('mst_mk', function (Blueprint $table) {
            $table->integer('sks')->nullable()->after('nama_mk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('mst_mk') || !Schema::hasColumn('mst_mk', 'sks')) {
            return;
        }

        Schema::table('mst_mk', function (Blueprint $table) {
            $table->dropColumn('sks');
        });
    }
};
