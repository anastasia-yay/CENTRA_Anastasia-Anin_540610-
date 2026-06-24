<?php

namespace App\Imports;

use App\Models\DisasterEvent;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Carbon\Carbon;

class DisasterEventsImport implements ToCollection, WithHeadingRow, WithValidation
{
    /**
     * Mengolah data excel dalam bentuk Collection
     * * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        // Bungkus dengan database transaction agar aman jika terjadi error di tengah jalan
        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {

                // Mengantisipasi format tanggal di Excel (baik berupa teks biasa maupun format Date bawaan Excel)
                $tanggal = $row['tanggal_kejadian'];
                if (is_numeric($tanggal)) {
                    // Jika format cell di excel berupa "Date", ia akan terbaca sebagai angka serial
                    $tanggal = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tanggal)->format('Y-m-d');
                } else {
                    $tanggal = Carbon::parse($tanggal)->format('Y-m-d');
                }

                // 1. Simpan data ke tabel disaster_events
                $event = DisasterEvent::create([
                    'disaster_type_id' => $row['disaster_type_id'],
                    'region_id'        => $row['region_id'],
                    'jumlah_kejadian'  => $row['jumlah_kejadian'] ?? 0,
                    'jumlah_korban'    => $row['jumlah_korban'] ?? 0,
                    'tanggal_kejadian' => $tanggal,
                    'status'           => $row['status'] ?? 'ACC',
                    'keterangan'       => $row['keterangan'] ?? null,
                ]);

                // 2. Update geom_actual otomatis mengambil nilai centroid wilayah (Sesuai spesifikasi Controller)
                DB::statement(
                    "UPDATE disaster_events
                    SET geom_actual = ST_Centroid(
                        (SELECT geom_centroid FROM regions WHERE id = ?)
                    )
                    WHERE id = ?",
                    [$event->region_id, $event->id]
                );
            }
        });
    }

    /**
     * Aturan validasi baris sebelum data dimasukkan ke database
     */
    public function rules(): array
    {
        return [
            '*.disaster_type_id' => ['required', 'exists:disaster_types,id'],
            '*.region_id'        => ['required', 'exists:regions,id'],
            '*.jumlah_kejadian'  => ['required', 'integer', 'min:0'],
            '*.jumlah_korban'    => ['required', 'integer', 'min:0'],
            '*.tanggal_kejadian' => ['required'],
            '*.status'           => ['nullable', 'string', 'max:50'],
            '*.keterangan'       => ['nullable', 'string'],
        ];
    }

    /**
     * Kustomisasi nama atribut error agar lebih mudah dibaca jika validasi gagal
     */
    public function customValidationAttributes()
    {
        return [
            '*.disaster_type_id' => 'ID Jenis Bencana',
            '*.region_id'        => 'ID Wilayah/Region',
            '*.jumlah_kejadian'  => 'Jumlah Kejadian',
            '*.jumlah_korban'    => 'Jumlah Korban',
            '*.tanggal_kejadian' => 'Tanggal Kejadian',
        ];
    }
}
