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
        Schema::create('tbl_tahap_mekanisme_penilaian', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('id_cpl',10);
            $table->string('id_mk',10);
            $table->string('id_cpmk',20);
            $table->string('tahap_penilaian',255);
            $table->string('teknik_penilaian',255);
            $table->string('instrumen',255);
            $table->string('kriteria',255);
            $table->decimal('bobot',8,2)->default(0);
            $table->decimal('bobot_mk',8,2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_tahap_mekanisme_penilaian');
    }
};
