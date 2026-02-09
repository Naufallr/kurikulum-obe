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
        Schema::create('tbl_cpl_mk', function (Blueprint $table) {
            $table->string('id_cpl', 5);
            $table->string('id_mk', 10);

            $table->primary(['id_cpl', 'id_mk']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_cpl_mk');
    }
};
