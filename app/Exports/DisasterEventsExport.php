<?php

namespace App\Exports;

use App\Models\DisasterEvent;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DisasterEventsExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * Ambil data bersama relasinya agar performa cepat (Eager Loading)
    */
    public function collection()
    {
        return DisasterEvent::with(['region', 'disasterTypes', 'riskLevel'])
            ->latest('tanggal_kejadian')
            ->get();
    }

    /**
     * Menentukan judul kolom paling atas di Excel
     */
    public function headings(): array
    {
        return [
            'ID Kejadian',
            'Jenis Bencana',
            'Wilayah/Region',
            'Tingkat Risiko',
            'Jumlah Kejadian',
            'Jumlah Korban',
            'Tanggal Kejadian',
            'Status',
            'Keterangan'
        ];
    }

    /**
     * Mengatur isi tiap kolom berdasarkan data model & relasinya
     */
    public function map($event): array
    {
        return [
            $event->id,
            $event->disasterTypes->nama_bencana ?? '-', // Sesuai relasi 'disasterTypes' di controller
            $event->region->nama_wilayah ?? '-',
            $event->riskLevel->nama_tingkat ?? '-',
            $event->jumlah_kejadian,
            $event->jumlah_korban,
            $event->tanggal_kejadian,
            $event->status,
            $event->keterangan,
        ];
    }
}
