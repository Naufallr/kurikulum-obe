<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mst_cpmk', function (Blueprint $table) {
            $table->string('id_cpl', 10);
            $table->text('desk_cpl');
            $table->string('id_cpmk', 20);
            $table->text('desk_cpmk');
            $table->primary('desk_cpmk');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mst_cpmk');
    }
};
