<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\DisasterTypeController;
use App\Http\Controllers\DisasterEventController;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Authenticated Area
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Interactive Map
    |--------------------------------------------------------------------------
    */
    // Route::get('/peta', [MapController::class, 'index'])
    //     ->name('map.index');

    Route::get('/peta', [MapController::class, 'index'])
        ->name('map.index');

    Route::get('/peta/choropleth', [MapController::class, 'choroplethPage'])
        ->name('map.choropleth');

    Route::get('/peta/heatmap', [MapController::class, 'heatmapPage'])
        ->name('map.heatmap');


    /*
    |--------------------------------------------------------------------------
    | API Map
    |--------------------------------------------------------------------------
    */
    Route::prefix('api/map')
        ->name('api.map.')
        ->group(function () {

            Route::get('/choropleth', [MapController::class, 'choropleth'])
                ->name('choropleth');

            Route::get('/centroid', [MapController::class, 'centroid'])
                ->name('centroid');
        });

    /*
    |--------------------------------------------------------------------------
    | API Regions
    |--------------------------------------------------------------------------
    */
    Route::prefix('api/regions')
        ->name('api.regions.')
        ->group(function () {

            Route::get('/', [RegionController::class, 'apiList'])
                ->name('list');

            Route::get('/{region}', [RegionController::class, 'apiShow'])
                ->name('show');
        });

    /*
    |--------------------------------------------------------------------------
    | Disaster Events
    |--------------------------------------------------------------------------
    */
    Route::resource(
        'kejadian',
        DisasterEventController::class
    )->names([
        'index'   => 'disaster-events.index',
        'create'  => 'disaster-events.create',
        'store'   => 'disaster-events.store',
        'show'    => 'disaster-events.show',
        'edit'    => 'disaster-events.edit',
        'update'  => 'disaster-events.update',
        'destroy' => 'disaster-events.destroy',
    ]);

    /*
    |--------------------------------------------------------------------------
    | Import Export
    |--------------------------------------------------------------------------
    */
    Route::prefix('kejadian')
        ->name('disaster-events.')
        ->group(function () {

            Route::get(
                '/import/form',
                [DisasterEventController::class, 'importForm']
            )->name('import.form');

            Route::post(
                '/import',
                [DisasterEventController::class, 'import']
            )->name('import');

            Route::get(
                '/export/excel',
                [DisasterEventController::class, 'exportExcel']
            )->name('export.excel');

            Route::get(
                '/export/pdf',
                [DisasterEventController::class, 'exportPdf']
            )->name('export.pdf');
        });

    /*
    |--------------------------------------------------------------------------
    | Disaster Types
    |--------------------------------------------------------------------------
    */
    // Route::resource(
    //     'jenis-bencana',
    //     DisasterTypeController::class
    // )->except(['show']);

    /*
    |--------------------------------------------------------------------------
    | Regions
    |--------------------------------------------------------------------------
    */
    Route::resource(
        'wilayah',
        RegionController::class
    )->except(['show']);

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */
    Route::prefix('profile')
        ->name('profile.')
        ->group(function () {

            Route::get('/', [ProfileController::class, 'edit'])
                ->name('edit');

            Route::patch('/', [ProfileController::class, 'update'])
                ->name('update');

            Route::delete('/', [ProfileController::class, 'destroy'])
                ->name('destroy');
        });
});

require __DIR__.'/auth.php';
