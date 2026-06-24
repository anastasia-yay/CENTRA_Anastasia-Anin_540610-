<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * PRASYARAT: Anda harus sudah mengunduh GeoJSON batas administrasi
 * Kabupaten/Kota Jawa Tengah dari BIG (Tanah Air) atau Ina-Geoportal.
 * Setiap feature harus memiliki properti 'KAB_KOTA' berisi nama wilayah
 * (contoh: "KLATEN", "KOTA SALATIGA", dsb — huruf kapital semua).
 *
 * Cara pakai:
 *   php artisan region:import-geojson storage/app/geo/jateng_kabkota.geojson
 */
class ImportRegionGeojson extends Command
{
    protected $signature = 'region:import-geojson {file}';
    protected $description = 'Import poligon batas wilayah dari GeoJSON ke kolom geom_polygon tabel regions';

    public function handle()
    {
        $path = $this->argument('file');

        if (!file_exists($path)) {
            $this->error("File tidak ditemukan: {$path}");
            return 1;
        }

        $geojson = json_decode(file_get_contents($path), true);

        if (!$geojson || !isset($geojson['features'])) {
            $this->error('File bukan GeoJSON FeatureCollection yang valid.');
            return 1;
        }

        $matched   = 0;
        $unmatched = [];

        foreach ($geojson['features'] as $feature) {
            // Ambil nama wilayah dari properti KAB_KOTA, normalkan ke huruf kapital
            $namaWilayah = strtoupper(
                trim($feature['properties']['KAB_KOTA'] ?? '')
            );

            if (!$namaWilayah) {
                $this->warn("Feature tanpa properti 'KAB_KOTA', dilewati.");
                continue;
            }

            // Tentukan jenis wilayah berdasarkan nama
            $jenisWilayah = str_contains($namaWilayah, 'KOTA') ? 'Kota' : 'Kabupaten';

            // Upsert baris region (insert jika belum ada, update jika sudah)
            DB::table('regions')->updateOrInsert(
                [
                    'nama_wilayah' => $namaWilayah,
                ],
                [
                    'jenis_wilayah' => $jenisWilayah,
                    'updated_at'    => now(),
                    'created_at'    => now(),
                ]
            );

            // Simpan geometri ke baris yang baru saja di-upsert
            $geometryJson = json_encode($feature['geometry']);

            $updated = DB::statement("
            UPDATE regions
            SET
                geom_polygon = ST_Multi(
                    ST_Force2D(
                        ST_SetSRID(
                            ST_GeomFromGeoJSON(?),
                            4326
                        )
                    )
                ),

                geom_centroid = ST_PointOnSurface(
                    ST_Force2D(
                        ST_SetSRID(
                            ST_GeomFromGeoJSON(?),
                            4326
                        )
                    )
                )

            WHERE nama_wilayah = ?
        ", [
            $geometryJson,
            $geometryJson,
            $namaWilayah,
        ]);

            $matched++;
        }

        $this->info("Berhasil memproses {$matched} wilayah.");

        if (count($unmatched) > 0) {
            $this->warn(
                'Wilayah dari GeoJSON yang GAGAL disimpan geometrinya: '
                . implode(', ', $unmatched)
            );
            $this->line('Pastikan kolom nama_wilayah di tabel regions sudah terisi dengan benar.');
        }

        return 0;
    }
}
