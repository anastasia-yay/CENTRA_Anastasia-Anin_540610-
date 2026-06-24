<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('risk_levels', function (Blueprint $table) {
            $table->id();
            $table->string('nama_tingkat'); // Tinggi, Sedang, Rendah
            $table->string('warna_hex', 7); // #FF0000 dst, dipakai untuk choropleth
            $table->unsignedTinyInteger('urutan')->default(0); // untuk sorting legenda
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risk_levels');
    }
};
