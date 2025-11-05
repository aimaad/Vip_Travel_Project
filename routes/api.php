<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Amadeus\Amadeus;
use Amadeus\Exceptions\ResponseException;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/flight-destinations', function () {
    // 1. Authentification OAuth2
    $authResponse = Http::asForm()->post('https://api.amadeus.com/v1/security/oauth2/token', [
        'grant_type' => 'client_credentials',
        'client_id' => config('services.amadeus.key'),
        'client_secret' => config('services.amadeus.secret')
    ]);

    if (!$authResponse->successful()) {
        return response()->json([
            'error' => 'Authentication failed',
            'details' => $authResponse->json()
        ], 401);
    }

    $accessToken = $authResponse->json()['access_token'];

    // 2. Appel à l'API Flight Inspiration Search
    $flightResponse = Http::withHeaders([
        'Authorization' => 'Bearer ' . $accessToken
    ])->get('https://api.amadeus.com/v1/shopping/flight-destinations', [
        'origin' => request('origin', 'PAR'), // PAR par défaut
        'maxPrice' => request('maxPrice', 200) // 200 EUR par défaut
    ]);

    // 3. Retour des résultats
    if ($flightResponse->successful()) {
        return $flightResponse->json();
    }

    return response()->json([
        'error' => 'Flight search failed',
        'details' => $flightResponse->json()
    ], $flightResponse->status());
});



Route::get('/check-logo', function(Request $request) {
    $request->validate(['domain' => 'required|string']);
    
    $logoUrl = 'https://logo.clearbit.com/'.$request->domain.'?size=80';
    
    try {
        $response = Http::head($logoUrl);
        return response()->json(['exists' => $response->ok()]);
    } catch (\Exception $e) {
        return response()->json(['exists' => false], 500);
    }
});


Route::get('/flight-results/status/{id}', function ($id) {
    $search = \App\Models\FlightSearchResultAmadeuse::find($id);

    return response()->json([
        'status' => $search?->status ?? 'not_found'
    ]);
})->name('api.flight.results.status');

Route::get('/airline/{iata}', function($iata) {
    $airline = \App\Models\Airline::where('iata_code', $iata)->first();
    if($airline) {
        return response()->json([
            'name' => $airline->name,
            'logoUrl' => 'https://logo.clearbit.com/' . $airline->domain . '?size=150'
        ]);
    }
    return response()->json([
        'name' => $iata,
        'logoUrl' => 'https://via.placeholder.com/100?text=' . $iata
    ]);
});
