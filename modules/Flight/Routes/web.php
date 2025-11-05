<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Modules\Flight\Admin\OfferController;
use Modules\Flight\Controllers\AirlineController;


Route::group(['prefix' => config('flight.flight_route_prefix')], function () {
    Route::get('/', 'FlightController@index')->name('flight.search'); // Search
    Route::post('getData/{id}', "FlightController@getData")->name('flight.getData');

    Route::get('/airport/search', 'AirportController@search')->name('flight.airport.search'); // Search
});

Route::group(['prefix' => 'user/' . config('flight.flight_route_prefix'), 'middleware' => ['auth', 'verified']], function () {
    Route::get('/', 'ManageFlightController@manageFlight')->name('flight.vendor.index');
    Route::get('/create', 'ManageFlightController@createFlight')->name('flight.vendor.create');
    Route::get('/edit/{id}', 'ManageFlightController@editFlight')->name('flight.vendor.edit');
    Route::get('/del/{id}', 'ManageFlightController@deleteFlight')->name('flight.vendor.delete');
    Route::post('/store/{id}', 'ManageFlightController@store')->name('flight.vendor.store');
    Route::get('bulkEdit/{id}', 'ManageFlightController@bulkEditFlight')->name("flight.vendor.bulk_edit");
    Route::get('/booking-report/bulkEdit/{id}', 'ManageFlightController@bookingReportBulkEdit')->name("flight.vendor.booking_report.bulk_edit");
    Route::get('clone/{id}', 'ManageFlightController@cloneFlight')->name("flight.vendor.clone");
    Route::get('/recovery', 'ManageFlightController@recovery')->name('flight.vendor.recovery');
    Route::get('/restore/{id}', 'ManageFlightController@restore')->name('flight.vendor.restore');

    Route::group(['prefix' => '{flight_id}/flight-seat'], function () {
        Route::get('/', 'ManageFlightSeatController@index')->name('flight.vendor.seat.index');
        Route::get('create', 'ManageFlightSeatController@create')->name('flight.vendor.seat.create');
        Route::get('edit/{id}', 'ManageFlightSeatController@edit')->name('flight.vendor.seat.edit');
        Route::post('store/{id}', 'ManageFlightSeatController@store')->name('flight.vendor.seat.store');
        Route::post('delete/{id}', 'ManageFlightSeatController@delete')->name('flight.vendor.seat.delete');
        Route::post('/bulkEdit', 'ManageFlightSeatController@bulkEdit')->name('flight.vendor.seat.bulkEdit');
    });
});

// Admin routes group
Route::group([
    'prefix' => config('admin.admin_route_prefix', 'admin'),
    'middleware' => ['auth', 'dashboard']
], function () {

    // Flight Offers routes
    Route::prefix('flight/offers')->group(function () {
        // Liste des offres
        Route::get('/', [OfferController::class, 'index'])
            ->name('flight.admin.offers.index');

        // Création
        Route::get('/create', [OfferController::class, 'create'])
            ->name('flight.admin.offers.create');

        // Soumission du formulaire
        Route::post('/', [OfferController::class, 'store'])
            ->name('flight.admin.offers.store');

        // Récupération AJAX du formulaire (CORRIGÉ)
        Route::post('/get-form', [OfferController::class, 'getForm'])
            ->name('flight.admin.offers.get_form');

        // Édition (AJOUT MANQUANT)
        Route::get('/{id}/edit', [OfferController::class, 'edit'])
            ->name('flight.admin.offers.edit');

        // Mise à jour
        Route::put('/{id}', [OfferController::class, 'update'])
            ->name('flight.admin.offers.update');

        // Suppression
        Route::delete('/{id}', [OfferController::class, 'destroy'])
            ->name('flight.admin.offers.destroy');

        Route::get('/flight/offers/{offer}/results', [OfferController::class, 'showResults'])
            ->name('flight.admin.offers.results');
    });

    // Logs (only for super admin)
    Route::get('/logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index')
        ->middleware('system_log_view')
        ->name('admin.logs');
});

// Airline
Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('airlines', AirlineController::class);

    Route::get('airlines/check-existing', function (Request $request) {
        $field = $request->query->keys()[0] ?? null;
        $value = $request->query($field);

        if (!$field || !$value) {
            return response()->json(['exists' => false]);
        }

        $exists = \App\Models\Airline::where(
            $field,
            $field === 'iata_code'
                ? strtoupper($value)
                : strtolower($value)
        )->exists();

        return response()->json(['exists' => $exists]);
    })->name('airlines.check-existing');
});