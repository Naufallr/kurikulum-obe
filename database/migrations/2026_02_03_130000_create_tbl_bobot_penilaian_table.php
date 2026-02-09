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
        Schema::create('tbl_bobot_penilaian', function (Blueprint $table) {
            $table->string('id_mk', 10);
            $table->string('nama_mk', 255);
            $table->string('id_cpl', 10);
            $table->string('id_cpmk', 10);
            $table->string('mbkm', 255)->nullable();
            $table->decimal('partisipasi', 5, 2)->nullable();
            $table->decimal('observasi', 5, 2)->nullable();
            $table->decimal('unjuk_kerja', 5, 2)->nullable();
            $table->decimal('tes_uts', 5, 2)->nullable();
            $table->decimal('tes_uas', 5, 2)->nullable();
            $table->decimal('tes_lisan', 5, 2)->nullable();
            $table->decimal('total', 5, 2)->nullable();
            $table->decimal('total_nilai_mk', 5, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_bobot_penilaian');
    }
};
