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
        Schema::create('tbl_pemetaan_cpl_cpmk_mk', function (Blueprint $table) {
            $table->string('id_cpl', 10);
            $table->text('desk_cpl');
            $table->string('id_cpmk', 20);
            $table->text('desk_cpmk');
            $table->string('id_mk', 10);
            $table->primary('desk_cpmk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_pemetaan_cpl_cpmk_mk');
    }
};
