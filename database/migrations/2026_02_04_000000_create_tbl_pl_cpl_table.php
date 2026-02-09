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
        Schema::create('tbl_pl_cpl', function (Blueprint $table) {
            $table->string('id_pl', 4);
            $table->string('id_cplp', 5);

            $table->primary(['id_pl', 'id_cplp']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_pl_cpl');
    }
};
