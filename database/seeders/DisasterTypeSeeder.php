<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DisasterType;

class DisasterTypeSeeder extends Seeder
{
    /**
     * Daftar ini diambil langsung dari kolom "Jenis Bencana" pada
     * CEVADIS_-_BPBD_Provinsi_Jawa_Tengah.xlsx (205 baris kejadian).
     * Bukan asumsi — semua nama persis seperti penulisan aslinya di Excel.
     */
    public function run(): void
    {
        $types = [
            ['nama_bencana' => 'Cuaca Esktrem', 'icon_path' => 'icons/cuaca-ekstrem.png'],
            ['nama_bencana' => 'Banjir', 'icon_path' => 'icons/banjir.png'],
            ['nama_bencana' => 'Karhutla', 'icon_path' => 'icons/karhutla.png'],
            ['nama_bencana' => 'Longsor', 'icon_path' => 'icons/longsor.png'],
            ['nama_bencana' => 'Kebakaran Gedung & Pemukiman', 'icon_path' => 'icons/kebakaran.png'],
            ['nama_bencana' => 'Kekeringan', 'icon_path' => 'icons/kekeringan.png'],
        ];

        foreach ($types as $type) {
            DisasterType::firstOrCreate(['nama_bencana' => $type['nama_bencana']], $type);
        }
    }
}
