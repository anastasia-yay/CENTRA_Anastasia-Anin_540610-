<?php

namespace App\Imports;

use App\Models\Region;
use App\Models\DisasterType;
use App\Models\DisasterEvent;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Carbon\Carbon;

/**
 * Memetakan kolom Excel CEVADIS:
 * No | ID | Kab/Kota | Jenis Bencana | Waktu Kejadian | Maps | Status
 *
 * Catatan struktur asli file:
 * - Baris 1: judul "CEVADIS - BPBD Provinsi Jawa Tengah" (skip)
 * - Baris 2: header kolom asli (skip)
 * - Baris 3 dst: data
 * - Kolom "Maps" kosong di semua baris -> belum ada koordinat aktual
 */
class CevadisImport implements ToModel, WithStartRow
{
    private array $regionCache = [];
    private array $typeCache   = [];

    public function startRow(): int
    {
        return 3; // skip baris judul & header
    }

    public function model(array $row)
    {
        // Susunan kolom: [No, ID, Kab/Kota, Jenis Bencana, Waktu Kejadian, Maps, Status, ...]
        [$no, $cevadisId, $namaWilayah, $jenisBencana, $waktuKejadian, $maps, $status]
            = array_pad($row, 7, null);

        if (!$cevadisId || !$namaWilayah) {
            return null; // skip baris kosong/rusak
        }

        $region = $this->resolveRegion((string) $namaWilayah);
            if (!$region) {
                return null;
            }
        $disasterType = $this->resolveDisasterType((string) $jenisBencana);
            if (!$disasterType) {
                return null;
            }
        $tanggal     = $this->parseTanggal($waktuKejadian);

        return new DisasterEvent([
            'cevadis_id'       => (string) $cevadisId,
            'region_id'        => $region?->id,
            'disaster_type_id' => $disasterType?->id,
            'jumlah_kejadian'  => 1,
            'tanggal_kejadian' => $tanggal,
            'tahun'            => $tanggal?->year,
            'jumlah_korban'    => 0, // tidak tersedia di Excel, isi manual jika ada data tambahan
            'status'           => $status ?: 'ACC',
        ]);
    }

    private function resolveRegion(string $nama): ?Region
    {
        $nama = trim($nama);

        if (isset($this->regionCache[$nama])) {
            return $this->regionCache[$nama];
        }

        // Bandingkan case-insensitive agar cocok dengan nama GeoJSON (huruf kapital semua)
        $region = Region::whereRaw('UPPER(nama_wilayah) = ?', [strtoupper($nama)])->first();

        return $this->regionCache[$nama] = $region;
    }

    private function resolveDisasterType(string $jenis): ?DisasterType
    {
        $jenis = trim($jenis);

        if (isset($this->typeCache[$jenis])) {
            return $this->typeCache[$jenis];
        }

        $type = DisasterType::whereRaw(
            'UPPER(nama_bencana) = ?',
            [strtoupper($jenis)]
        )->first();

        return $this->typeCache[$jenis] = $type;
    }

    /**
     * Format asli di Excel: "2024-12-09 13:40 WIB"
     * Suffix "WIB" di-strip karena Carbon tidak mengenali zona waktu teks tersebut.
     */
    private function parseTanggal($value): ?Carbon
    {
        if (!$value) {
            return null;
        }

        $clean = trim(str_ireplace('WIB', '', (string) $value));

        try {
            return Carbon::parse($clean);
        } catch (\Exception $e) {
            return null;
        }
    }
}
