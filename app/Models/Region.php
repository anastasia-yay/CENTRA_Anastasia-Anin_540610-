<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Region extends Model
{
    protected $fillable = [
        'kode_wilayah',
        'nama_wilayah',
        'jenis_wilayah',
    ];

    public function disasterEvents()
    {
        return $this->hasMany(DisasterEvent::class);
    }

    // ══════════════════════════════════════════════════════════════
    // geojsonChoropleth()
    //
    // PERUBAHAN vs versi sebelumnya:
    // [1] Filter $jenis dipindahkan KE DALAM closure leftJoin()
    //     menggunakan $join->where(...), bukan di WHERE query utama.
    //     Ini memastikan LEFT JOIN tetap berperilaku LEFT JOIN:
    //     wilayah dengan 0 kejadian tetap muncul (total_kejadian = 0),
    //     bukan hilang akibat WHERE yang mengubahnya menjadi INNER JOIN.
    // [2] Baris `if ($jenis !== null) { $query->where(...) }` dihapus.
    // [3] Style polygon `dashArray:'3'` kini dikendalikan di sisi JS
    //     berdasarkan nilai total_kejadian, bukan di sini.
    // ══════════════════════════════════════════════════════════════
    public static function geojsonChoropleth(?int $jenis = null): array
    {
        $rows = DB::table('regions as r')
            ->select(
                'r.id',
                'r.kode_wilayah',
                'r.nama_wilayah',
                'r.jenis_wilayah',
                DB::raw('ST_AsGeoJSON(r.geom_polygon) as geojson'),
                DB::raw('COALESCE(SUM(de.jumlah_kejadian), 0) as total_kejadian')
            )
            // [1] Kondisi filter jenis masuk ke dalam join — bukan WHERE
            ->leftJoin('disaster_events as de', function ($join) use ($jenis) {
                $join->on('de.region_id', '=', 'r.id');

                if ($jenis !== null) {
                    $join->where('de.disaster_type_id', '=', $jenis);
                }
            })
            ->groupBy(
                'r.id',
                'r.kode_wilayah',
                'r.nama_wilayah',
                'r.jenis_wilayah',
                'r.geom_polygon'
            )
            ->get();

        return [
            'type'     => 'FeatureCollection',
            'features' => $rows
                ->map(function ($row) {
                    if (!$row->geojson) {
                        return null; // wilayah tanpa geometri dilewati
                    }

                    return [
                        'type'       => 'Feature',
                        'geometry'   => json_decode($row->geojson),
                        'properties' => [
                            'id'             => $row->id,
                            'kode_wilayah'   => $row->kode_wilayah,
                            'nama_wilayah'   => $row->nama_wilayah,
                            'jenis_wilayah'  => $row->jenis_wilayah,
                            'total_kejadian' => (int) $row->total_kejadian,
                        ],
                    ];
                })
                ->filter()
                ->values(),
        ];
    }

    // ══════════════════════════════════════════════════════════════
    // geojsonCentroid()
    //
    // PERUBAHAN vs versi sebelumnya:
    // [1] Sama seperti geojsonChoropleth(): filter $jenis masuk ke
    //     dalam closure leftJoin() — bukan WHERE query utama.
    // [2] Baris `if ($jenis !== null) { $query->where(...) }` dihapus.
    // [3] Syarat `$row->total_kejadian <= 0` pada filter null DIHAPUS.
    //     Centroid wilayah dengan nilai 0 tetap dikirim ke JS.
    //     Filtering nilai 0 (tidak perlu ditampilkan di heatmap)
    //     dilakukan di sisi JavaScript, bukan di sini.
    //     Ini agar klasifikasi Equal Interval di JS tetap akurat
    //     karena menghitung dari seluruh wilayah yang terdapat data.
    // ══════════════════════════════════════════════════════════════
    public static function geojsonCentroid(?int $jenis = null): array
    {
        $rows = DB::table('regions as r')
            ->select(
                'r.id',
                'r.nama_wilayah',
                DB::raw("
                    ST_AsGeoJSON(
                        COALESCE(
                            r.geom_centroid,
                            ST_PointOnSurface(r.geom_polygon)
                        )
                    ) as geojson
                "),
                DB::raw('COALESCE(SUM(de.jumlah_kejadian), 0) as total_kejadian')
            )
            // [1] Kondisi filter jenis masuk ke dalam join
            ->leftJoin('disaster_events as de', function ($join) use ($jenis) {
                $join->on('de.region_id', '=', 'r.id');

                if ($jenis !== null) {
                    $join->where('de.disaster_type_id', '=', $jenis);
                }
            })
            ->groupBy(
                'r.id',
                'r.nama_wilayah',
                'r.geom_centroid',
                'r.geom_polygon'
            )
            ->get();

        return [
            'type'     => 'FeatureCollection',
            'features' => $rows
                ->map(function ($row) {
                    // [3] Hanya lewati wilayah tanpa geometri sama sekali
                    if (!$row->geojson) {
                        return null;
                    }

                    return [
                        'type'       => 'Feature',
                        'geometry'   => json_decode($row->geojson),
                        'properties' => [
                            'id'             => $row->id,
                            'nama_wilayah'   => $row->nama_wilayah,
                            'total_kejadian' => (int) $row->total_kejadian,
                        ],
                    ];
                })
                ->filter()
                ->values(),
        ];
    }
}
