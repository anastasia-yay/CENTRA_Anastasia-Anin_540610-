<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CevadisImport;

class ImportCevadisData extends Command
{
    protected $signature = 'cevadis:import {file}';
    protected $description = 'Import data kejadian bencana dari Excel CEVADIS BPBD Jateng ke tabel disaster_events';

    public function handle()
    {
        $path = $this->argument('file');

        if (!file_exists($path)) {
            $this->error("File tidak ditemukan: {$path}");
            return 1;
        }

        $this->info('Memulai import dari: ' . $path);

        Excel::import(new CevadisImport, $path);

        $total = DB::table('disaster_events')->count();
        $this->info("Selesai. Total baris di disaster_events sekarang: {$total}");

        return 0;
    }
}
