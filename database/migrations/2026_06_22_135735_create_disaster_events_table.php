<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disaster_events', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Relasi
            |--------------------------------------------------------------------------
            */

            $table->foreignId('region_id')
                ->nullable()
                ->constrained('regions')
                ->nullOnDelete();

            $table->foreignId('disaster_type_id')
                ->nullable()
                ->constrained('disaster_types')
                ->nullOnDelete();

            $table->foreignId('risk_level_id')
                ->nullable()
                ->constrained('risk_levels')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Data Utama
            |--------------------------------------------------------------------------
            */

            $table->string('cevadis_id', 30)
                ->nullable()
                ->unique();

            $table->date('tanggal_kejadian');

            $table->unsignedInteger('jumlah_kejadian')
                ->default(1);

            $table->unsignedInteger('jumlah_korban')
                ->default(0);

            $table->year('tahun')
                ->nullable();

            $table->string('status', 50)
                ->default('Aktif');

            $table->text('keterangan')
                ->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Index
            |--------------------------------------------------------------------------
            */

            $table->index('region_id');
            $table->index('disaster_type_id');
            $table->index('risk_level_id');
            $table->index('tanggal_kejadian');
            $table->index('tahun');
        });

        /*
        |--------------------------------------------------------------------------
        | PostGIS Geometry
        |--------------------------------------------------------------------------
        */

        DB::statement("
            ALTER TABLE disaster_events
            ADD COLUMN geom_actual geometry(Point,4326)
        ");

        DB::statement("
            CREATE INDEX disaster_events_geom_idx
            ON disaster_events
            USING GIST(geom_actual)
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('disaster_events');
    }
};
