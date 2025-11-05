<?php

use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::get('/intro', 'LandingpageController@index');
Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index')->name('home');
Route::post('/install/check-db', 'HomeController@checkConnectDatabase');

// Social Login
Route::get('social-login/{provider}', 'Auth\LoginController@socialLogin');
Route::get('social-callback/{provider}', 'Auth\LoginController@socialCallBack');



  
// Installation routes
Route::get('/install', 'InstallerController@redirectToRequirement')->name('LaravelInstaller::welcome');
Route::get('/install/environment', 'InstallerController@redirectToWizard')->name('LaravelInstaller::environment');

// Password setup
Route::get('/set-password', 'Auth\SetPasswordController@showSetPasswordForm')->name('user.setPassword');
Route::post('/set-password/{id}/{hash}', 'Auth\SetPasswordController@savePassword')->name('user.savePassword');

// Hide update pages
Route::get('/update', 'InstallerController@redirectToHome');
Route::get('/update/overview', 'InstallerController@redirectToHome');
Route::get('/update/database', 'InstallerController@redirectToHome');

// Fallback route
Route::fallback([\Modules\Core\Controllers\FallbackController::class, 'FallBack']);
use App\Http\Controllers\HotelController;
use App\Http\Controllers\Admin\OffreController;
Route::get('/hotels/create', [HotelController::class, 'create'])
    ->middleware('offer.created')
    ->name('hotels.create');

Route::post('/hotels', [HotelController::class, 'store'])->name('hotels.store');
// Route pour confirmer et enregistrer l'hôtel
Route::post('/hotels/confirm/store', [HotelController::class, 'confirmStore'])->name('hotels.confirm.store');
Route::get('/hotels/complete/{id}', [HotelController::class, 'complete'])->name('hotels.complete');
Route::post('/hotels/complete/{id}', [HotelController::class, 'completeStore'])->name('hotels.complete.store');

// service
Route::get('/hotels/services', [HotelController::class, 'services'])->name('hotels.services');
Route::post('/hotels/store-services', [HotelController::class, 'confirm'])->name('hotels.store.services');
Route::post('/hotels/confirm-store', [HotelController::class, 'confirmStore'])->name('hotels.confirm.store');

//offre admin

Route::get('/admin/offres/{offre}/validation', [OffreController::class, 'validation'])
    ->name('admin.offres.validation');


    Route::get('/admin/offres/{offre}/validation', [OffreController::class, 'validation'])->name('admin.offres.validation');
Route::post('/admin/offres/{offre}/valider', [OffreController::class, 'valider'])->name('admin.offres.valider');
Route::post('/admin/offres/{offre}/refuser', [OffreController::class, 'refuser'])->name('admin.offres.refuser');
Route::get('/admin/offres', [App\Http\Controllers\Admin\OffreController::class, 'index'])->name('admin.offres.index');
Route::get('/mes-offres', [HotelController::class, 'mesOffres'])->name('mes.offres');

// Détail du refus de l'offre pour les clients
Route::get('/hotels/offres/{offre}/refus', [App\Http\Controllers\HotelController::class, 'detailRefus'])
    ->name('offre.refus.detail');


    Route::get('/hotels/confirm/{offre}', [HotelController::class, 'showConfirmForm'])->name('hotels.confirm');
// Affichage du détail d'une offre validée pour l'utilisateur
Route::get('/hotels/offres/{offre}/validee', [App\Http\Controllers\HotelController::class, 'detailValidee'])->name('offre.detail');



Route::get('/mes-offres', [HotelController::class, 'mesOffres'])->name('mes.offres');

// Création
Route::get('/offres/create', [HotelController::class, 'create'])->name('offre.create');
Route::post('/offres', [HotelController::class, 'store'])->name('offre.store');

// Confirmation (pour création, édition, duplication)
Route::get('/offres/{id}/edit', [HotelController::class, 'edit'])->name('offre.edit'); // Edition/doublon
Route::patch('/offres/{id}/update', [HotelController::class, 'updateConfirmation'])->name('offre.updateConfirmation');

// Duplication (redirige vers edit)
Route::get('/offres/{id}/dupliquer', [HotelController::class, 'duplicate'])->name('offre.duplicate');

// Arrêter
Route::patch('/offres/{id}/arreter', [HotelController::class, 'stop'])->name('offre.stop');

// Archiver
Route::patch('/offres/{id}/archiver', [HotelController::class, 'archive'])->name('offre.archive');

// Détail
Route::get('/offres/{id}/detail', [HotelController::class, 'detailValidee'])->name('offre.detail');
Route::get('/offres/{id}/refus', [HotelController::class, 'detailRefus'])->name('offre.refus.detail');




use App\Http\Controllers\OffreClientController;
use App\Http\Controllers\ReservationClientController;
use App\Http\Controllers\Modules\Flight\Admin\OfferController;

// Liste des offres validées et visibles par les clients
Route::get('/offres', [OffreClientController::class, 'index'])->name('offres.index');

// Fiche détail d’une offre
Route::get('/offres/{id}', [OffreClientController::class, 'show'])->name('offres.show');

// Envoi du formulaire de réservation (POST)
Route::post('/offres/{id}/reserver', [ReservationClientController::class, 'store'])->name('offres.reserver');

// (Optionnel) Affichage d'une page de confirmation après réservation
// Route::get('/reservation/confirmation', [ReservationClientController::class, 'confirmation'])->name('reservation.confirmation');
// Voir la liste des réservations de l'utilisateur connecté (optionnel)
// Route::get('/mes-reservations', [ReservationClientController::class, 'mesReservations'])->name('reservations.mes');

Route::post('/admin/flight/offers', [OfferController::class, 'store'])
    ->name('flight.admin.offers.store');

Route::post('/admin/flight-offers/direct-single', [OfferController::class, 'storeDirectSingleOffer'])->name('flight.admin.offers.direct_single');
Route::get('/admin/flight-search-results/{id}', [OfferController::class, 'pollSearchResult']);
Route::get('/flight-results/{id}', [OfferController::class, 'show'])->name('flight.results.show');
Route::get('/flight-test-result/{id}', function($id){
    $result = \App\Models\FlightSearchResultAmadeuse::findOrFail($id);
    return view('flight.test_result', ['result' => $result]);
});

Route::get('/admin/flight/offers/amadeus-results', [OfferController::class, 'fetchAmadeusResults'])->name('flight.admin.offers.results');
Route::get('/checkDirectMultipleResults/{token}', [OfferController::class, 'checkDirectMultipleResults'])
    ->name('flight.admin.offers.check-direct-multiple');


   
 Route::get('/admin/flight-offers/multi-flight/result/{id}', function ($id) {
        $search = \App\Models\FlightSearch::find($id);
        if (!$search) {
            return response()->json(['status' => 'error', 'message' => 'Recherche introuvable'], 404);
        }
    
        return response()->json([
            'status' => $search->status,
            'results' => $search->results,
            'error_message' => $search->error_message
        ]);
    });
    


    Route::post('/flights/updateDirectSingle', [OfferController::class, 'updateDirectSingle'])->name('flights.updateDirectSingle');
    Route::post('/flights/update-multiple', [OfferController::class, 'updateMultiple'])->name('flights.updateMultiple');


