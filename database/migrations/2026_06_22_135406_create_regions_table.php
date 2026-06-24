<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            // kode_wilayah berasal dari Excel CEVADIS (4 digit awal kolom ID),
            // nullable karena GeoJSON sumber (Jawa_Tengah_KabKota.geojson) TIDAK
            // menyediakan kode BPS sama sekali, hanya properti teks "KAB_KOTA".
            $table->string('kode_wilayah', 10)->nullable()->unique();
            $table->string('nama_wilayah')->unique(); // contoh: Klaten — dipakai sebagai kunci pencocokan utama
            // tambahan
            $table->string('jenis_wilayah')->nullable();
            $table->timestamps();
        });

        // Tambah kolom geometry via raw SQL (PostGIS belum disupport native oleh Blueprint)
        DB::statement('ALTER TABLE regions ADD COLUMN geom_polygon geometry(MultiPolygon,4326)');
        DB::statement('ALTER TABLE regions ADD COLUMN geom_centroid geometry(Point,4326)');

        // Index spasial agar query ST_Intersects / ST_Contains lebih cepat
        DB::statement('CREATE INDEX regions_geom_polygon_idx ON regions USING GIST (geom_polygon)');
        DB::statement('CREATE INDEX regions_geom_centroid_idx ON regions USING GIST (geom_centroid)');
    }

    public function down(): void
    {
        Schema::dropIfExists('regions');
    }
};
