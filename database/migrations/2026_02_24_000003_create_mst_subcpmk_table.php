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
        if (Schema::hasTable('mst_subcpmk')) {
            return;
        }

        Schema::create('mst_subcpmk', function (Blueprint $table) {
            $table->string('id_cpl', 10);
            $table->string('id_cpmk', 20);
            $table->text('desk_cpmk');
            $table->string('id_subcpmk', 30);
            $table->text('desk_subcpmk');
            $table->primary('id_subcpmk');
            $table->unique('desk_subcpmk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_subcpmk');
    }
};
