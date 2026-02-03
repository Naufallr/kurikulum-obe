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
        Schema::create('tbl_pemetaan_mk_cpmk_subcpmk', function (Blueprint $table) {
            $table->string('id_mk', 10);
            $table->string('id_cpl', 10);
            $table->string('id_cpmk', 10);
            $table->text('desk_cpmk');
            $table->string('sub_cpmk', 20);
            $table->text('desk_subcpmk');

            // Mengikuti pola table lain: gunakan field text sebagai primary key untuk route-model binding
            $table->primary('desk_subcpmk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_pemetaan_mk_cpmk_subcpmk');
    }
};
