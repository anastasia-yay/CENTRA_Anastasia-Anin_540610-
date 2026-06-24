<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DisasterEvent extends Model
{
    protected $fillable = [
        'cevadis_id',
        'region_id',
        'disaster_type_id',
        'risk_level_id',
        'jumlah_kejadian',
        'tanggal_kejadian',
        'jumlah_korban',
        'status',
        'keterangan',
        'geom_actual',
    ];

    protected $casts = [
        'tanggal_kejadian' => 'datetime',
    ];

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function riskLevel()
    {
        return $this->belongsTo(RiskLevel::class, 'risk_level_id');
    }

    public function disasterTypes()
    {
        return $this->belongsTo(DisasterType::class, 'disaster_type_id');
    }

    // /**
    //  * Titik lokasi aktual kejadian (hasil input Leaflet Draw) dalam GeoJSON.
    //  * Hanya kejadian yang sudah punya geom_actual yang akan muncul di layer ini.
    //  */
    // public static function geojsonActualPoints(?int $tahun = null, ?int $disasterTypeId = null)
    // {
    //     $query = DB::table('disaster_events as de')
    //         ->select(
    //             'de.id',
    //             'de.cevadis_id',
    //             'de.tanggal_kejadian',
    //             'de.jumlah_korban',
    //             DB::raw('ST_AsGeoJSON(de.geom_actual) as geojson'),
    //             'r.nama_wilayah',
    //             'dt.nama_bencana'
    //         )
    //         ->leftJoin('regions as r', 'r.id', '=', 'de.region_id')
    //         ->leftJoin('disaster_types as dt', 'dt.id', '=', 'de.disaster_type_id')
    //         ->whereNotNull('de.geom_actual');

    //     if ($tahun) {
    //         $query->where('de.tahun', $tahun);
    //     }
    //     if ($disasterTypeId) {
    //         $query->where('de.disaster_type_id', $disasterTypeId);
    //     }

    //     $rows = $query->get();

    //     $geojson = ['type' => 'FeatureCollection', 'features' => []];

    //     foreach ($rows as $row) {
    //         $geojson['features'][] = [
    //             'type' => 'Feature',
    //             'geometry' => json_decode($row->geojson),
    //             'properties' => [
    //                 'id' => $row->id,
    //                 'cevadis_id' => $row->cevadis_id,
    //                 'nama_wilayah' => $row->nama_wilayah,
    //                 'nama_bencana' => $row->nama_bencana,
    //                 'tanggal_kejadian' => $row->tanggal_kejadian,
    //                 'jumlah_korban' => $row->jumlah_korban,
    //             ],
    //         ];
    //     }

    //     return $geojson;
    // }
}
