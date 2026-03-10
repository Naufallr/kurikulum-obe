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
        Schema::create('mst_mk', function (Blueprint $table) {
            $table->string('id',10);
            $table->string('nama_mk',255)->primary();
            $table->integer('sks',10);
            $table->string('jenis_mk',255);
            $table->string('status',30);
            $table->integer('tahun',10);
            $table->unsignedTinyInteger('semester');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
        });
    }

    /**

    * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_mk');
    }
};
