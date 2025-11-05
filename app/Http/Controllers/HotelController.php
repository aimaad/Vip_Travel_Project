<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHotelRequest;
use Illuminate\Support\Facades\DB;
use App\Models\Hotel;
use App\Models\RoomType;
use App\Services\BookingScraperService;
use Illuminate\Http\Request;
use App\Jobs\ScrapeHotelData;
use App\Models\HotelScraping;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Modules\Flight\Admin\OfferController;
use App\Models\FlightSearch;

class HotelController extends Controller
{
    public function create()
    {
        return view('hotels.create');
    }

   public function store(StoreHotelRequest $request, BookingScraperService $scraper)
    {
        // Pas de transaction maintenant, on attend la confirmation finale
        try {
            $totalRooms = (int) $request->total_rooms;
            $sumAvailable = 0;
            $hotelName = $request->name; 

        
    
            // Vérification métier 1
            foreach ($request->room_types as $room) {
                $sumAvailable += (int) $room['available_rooms'];
            }

            if ($sumAvailable !== $totalRooms) {
                return back()->withErrors([
                    'room_types' => 'La somme des chambres en vente doit être exactement égale au nombre total de chambres (' . $totalRooms . ').'
                ])->withInput();
            }
    
            // Vérification métier 2
            foreach ($request->room_types as $room) {
                $type = strtolower($room['type']);
                $adults = (int) $room['adults'];
                $children = (int) $room['children'];
                $kids = (int) $room['kids'];
                $babies = (int) $room['babies'];
                $totalMinors = $children + $kids + $babies;
    
                if ($type === 'single' && ($adults > 1 || $totalMinors > 3)) {
                    return back()->withErrors([
                        'room_types' => 'Chambre SINGLE : max 1 adulte et 3 mineurs (enfant/kid/bébé).'
                    ])->withInput();
                }
    
                if ($type === 'double' && ($adults > 2 || $totalMinors > 2)) {
                    return back()->withErrors([
                        'room_types' => 'Chambre DOUBLE : max 2 adultes et 2 mineurs (enfant/kid/bébé).'
                    ])->withInput();
                }
    
                if ($type === 'triple' && ($adults > 3 || $babies > 1 || ($children + $kids) > 0)) {
                    return back()->withErrors([
                        'room_types' => 'Chambre TRIPLE : max 3 adultes et 1 bébé seulement (pas d’enfant ou kid).'
                    ])->withInput();
                }
            }
    
            // ✅ Si tout est OK, on déclenche le scraping avec l'URL de l'hôtel
            $hotelName = $request->name;
            $city = $request->city;
            $existingScraping = HotelScraping::where('hotel_name', $hotelName)->first();

            
            if (!$existingScraping) {
                // 🛠 Lancer le scraping SEULEMENT si aucune donnée existante
                ScrapeHotelData::dispatch($hotelName, $city)->onQueue('default');
                \Log::info("Scraping lancé pour {$hotelName} à {$city}");
            } else {
                \Log::info("Scraping déjà existant pour {$hotelName}, on ne lance pas le job.");
            }
            session([
                'input_hotel' => $request->all()
            ]);
    
            // Rediriger vers l’étape suivante : ajouter les services
            return redirect()->route('hotels.services');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Une erreur est survenue : ' . $e->getMessage()]);
        }
    }
    
    private function normalizeFlightResults(?array $json, ?string $type): array
{
    if (!$json || !is_array($json)) {
        return [];
    }

    if ($type === 'direct_single') {
        // Vérifie que la structure correspond bien à celle attendue
        if (isset($json['status']) && isset($json['data'])) {
            return $json;
        }
    }

    if ($type === 'direct_multiple') {
        // Adapter la structure à un format similaire à direct_single pour affichage
        return [
            'status' => 'success',
            'data' => [
                [
                    'meta' => [
                        'count' => count($json['flights'] ?? []),
                    ],
                    'data' => [
                        'flights' => $json['flights'] ?? [],
                        'return_flights' => $json['return_flights'] ?? [],
                    ]
                ]
            ]
        ];
    }

    

    return $json;
}

public function confirm(Request $request)
{
    $input = session('input_hotel');
    $services = $request->input('services', []);
    if (empty($input['room_types']) || !is_array($input['room_types'])) {
        $input['room_types'] = [];
    }

    // Ajout pour éviter l’erreur
    $scraped = HotelScraping::where('hotel_name', $input['name'])->first();

    // Convertir en tableau si c’est un modèle
    $scraped = $scraped ? $scraped->toArray() : [];
    // STATUT DE SCRAPING
if (empty($scraped)) {
    $scrapingStatus = 'pending'; // pas encore reçu de scraping
} elseif (
    (empty($scraped['address']) || $scraped['address'] === 'Adresse non trouvée') ||
    (empty($scraped['rating']) || $scraped['rating'] === 'Note non trouvée') ||
    (empty($scraped['images']) || (is_array($scraped['images']) && count($scraped['images']) === 0))
) {
    $scrapingStatus = 'error'; // scraping a échoué
} else {
    $scrapingStatus = 'success'; // scraping OK
}

$flightResult = \App\Models\FlightSearch::where('user_id', auth()->id())
->orderByDesc('created_at')
->first();

$flightType = $flightResult->type ?? null;
$flightResultsHtml = null;

if ($flightResult && $flightResult->results) {
$raw = $flightResult->results;

// Double décodage si nécessaire
if (is_string($raw)) {
    $decodedOnce = json_decode($raw, true);

    if (is_string($decodedOnce)) {
        $json = json_decode($decodedOnce, true);
    } else {
        $json = $decodedOnce;
    }
} elseif (is_array($raw)) {
    $json = $raw;
} else {
    $json = [];
}

// 👉 Ici on prend directement toute la structure (avec flights / return_flights)
$flightResultsHtml = $this->normalizeFlightResults($json, $flightType);

}




    return view('hotels.confirmation', [
        'input' => $input,
        'services' => $services,
        'scraped' => $scraped,
        'scrapingStatus' => $scrapingStatus, 
        'flightResultsHtml' => $flightResultsHtml,
        'flightUser' => $flightResult,
        'flightSearch' => $flightResult->search_params['offers'] ?? [],
        'flightType'=> $flightType,

    ]);
}


public function confirmStore(Request $request)
{
    // 1. Décoder les infos du formulaire
    $input = json_decode($request->input('input'), true);
    $services = json_decode($request->input('services'), true);
    $booking = json_decode($request->input('booking'), true);

    // 2. Mettre à jour le scraping
    $hotelScraping = HotelScraping::where('hotel_name', $input['name'])->first();
    if ($hotelScraping) {
        $hotelScraping->address = $booking['address'] ?? $hotelScraping->address;
        $hotelScraping->rating = $booking['rating'] ?? $hotelScraping->rating;
        $hotelScraping->images = $booking['images'] ?? $hotelScraping->images;
        $hotelScraping->save();
    }

    // 3. Vérifications métier chambres
    $totalRooms = isset($input['total_rooms']) ? (int)$input['total_rooms'] : 0;
    $sumAvailable = 0;
    $roomTypes = $input['room_types'] ?? [];

    $scraped = $hotelScraping ? $hotelScraping->toArray() : [];
    if (empty($scraped)) {
        $scrapingStatus = 'pending';
    } elseif (
        (empty($scraped['address']) || $scraped['address'] === 'Adresse non trouvée') ||
        (empty($scraped['rating']) || $scraped['rating'] === 'Note non trouvée') ||
        (empty($scraped['images']) || (is_array($scraped['images']) && count($scraped['images']) === 0))
    ) {
        $scrapingStatus = 'error';
    } else {
        $scrapingStatus = 'success';
    }

    foreach ($roomTypes as $room) {
        $sumAvailable += (int) ($room['available_rooms'] ?? 0);
    }
    if ($sumAvailable !== $totalRooms) {
        return view('hotels.confirmation', [
            'input' => $input,
            'services' => $services,
            'scrapingStatus' => $scrapingStatus,
            'scraped' => $hotelScraping ? $hotelScraping->toArray() : [],
        ])->withErrors([
            'room_types' => 'La somme des chambres en vente doit être exactement égale au nombre total de chambres (' . $totalRooms . ').'
        ]);
    }

    foreach ($roomTypes as $room) {
        $type = strtolower($room['type'] ?? '');
        $adults = (int) ($room['adults'] ?? 0);
        $children = (int) ($room['children'] ?? 0);
        $kids = (int) ($room['kids'] ?? 0);
        $babies = (int) ($room['babies'] ?? 0);
        $totalMinors = $children + $kids + $babies;

        if ($type === 'single' && ($adults > 1 || $totalMinors > 3)) {
            return view('hotels.confirmation', [
                'input' => $input,
                'services' => $services,
                'scrapingStatus' => $scrapingStatus,
                'scraped' => $hotelScraping ? $hotelScraping->toArray() : [],
            ])->withErrors([
                'room_types' => 'Chambre SINGLE : max 1 adulte et 3 mineurs (enfant/kid/bébé).'
            ]);
        }
        if ($type === 'double' && ($adults > 2 || $totalMinors > 2)) {
            return view('hotels.confirmation', [
                'input' => $input,
                'services' => $services,
                'scrapingStatus' => $scrapingStatus,
                'scraped' => $hotelScraping ? $hotelScraping->toArray() : [],
            ])->withErrors([
                'room_types' => 'Chambre DOUBLE : max 2 adultes et 2 mineurs (enfant/kid/bébé).'
            ]);
        }
        if ($type === 'triple' && ($adults > 3 || $babies > 1 || ($children + $kids) > 0)) {
            return view('hotels.confirmation', [
                'input' => $input,
                'services' => $services,
                'scrapingStatus' => $scrapingStatus,
                'scraped' => $hotelScraping ? $hotelScraping->toArray() : [],
            ])->withErrors([
                'room_types' => 'Chambre TRIPLE : max 3 adultes et 1 bébé seulement (pas d’enfant ou kid).'
            ]);
        }
    }
    // ======= FIN VÉRIFICATIONS MÉTIERS =======

    // 4. Extraction des infos vol depuis FlightSearch du user
    $flightResult = \App\Models\FlightSearch::where('user_id', auth()->id())
        ->orderByDesc('created_at')
        ->first();

    // 5. Préparation des résultats de vol
    $flightResultsHtml = null;
    $searchParams = [];
    $flight_type = null;

    if ($flightResult) {
        $searchParams = is_string($flightResult->search_params) ? json_decode($flightResult->search_params, true) : $flightResult->search_params;
        $flight_type = $searchParams['type'] ??$searchParams['flight_type']?? null;
        $raw = $flightResult->results;
        if (is_string($raw)) {
            $json = json_decode($raw, true);
        } else {
            $json = $raw;
        }
        if (isset($json['data'][0]['data'])) {
            $flightResultsHtml = $json['data'][0]['data'];
        } elseif (isset($json['offers']) || isset($json[0]['outbound'])) {
            $flightResultsHtml = $json;
        } else {
            $flightResultsHtml = $json;
        }
    }

    // 6. Services
    $serviceIds = [];
    foreach ($services as $service) {
        $id = \DB::table('services')->insertGetId([
            'type_service' => $service['type_service'] ?? null,
            'date_service' => $service['date_service'] ?? null,
            'description' => $service['description'] ?? null,
            'prix' => $service['prix'] ?? null,
            'capacite' => $service['capacite'] ?? null,
            'type' => $service['type'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $serviceIds[] = $id;
    }

    // 7. Insertion dans la table offres (seulement les champs principaux)
    $offre_id = \DB::table('offres')->insertGetId([
        'hotel_scraping_id' => $hotelScraping?->id,
        'user_id' => auth()->id(),
        'total_rooms' => $input['total_rooms'],
        'room_types' => json_encode($input['room_types']),
        'service_ids' => json_encode($serviceIds),
        
      
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // 8. Insertion des blocs vol et des legs
    $offre = \App\Models\Offre::find($offre_id);
    Log::info('flight_type detected', ['flight_type' => $flight_type]);
       Log::info('flightResultsHtml', $flightResultsHtml);
    if ($flight_type === 'direct_multiple') {
        $offerFlight = $offre->offerFlights()->create([
            'places' => $flightResult->places ?? null,
            'price_adult' => $flightResult->price_adult ?? null,
            'price_child' => $flightResult->price_child ?? null,
            'price_baby' => $flightResult->price_baby ?? null,
            'flight_type' => $flight_type,
        ]);
    
  
    
        // Allers
        $flightGroups = $flightResultsHtml['flights'] ?? [];
        foreach ($flightGroups as $group) {
            foreach ($group as $offer) {
                $itineraries = $offer['itineraries'] ?? [];
                foreach ($itineraries as $itinerary) {
                    foreach ($itinerary['segments'] as $segment) {
                        $carrierCode = $segment['carrierCode'] ?? null;
                        $flightNumber = $segment['number'] ?? null;
                        $departure = $segment['departure'] ?? [];
                        $arrival = $segment['arrival'] ?? [];
    
                        $departureAt = isset($departure['at']) ? new \DateTime($departure['at']) : null;
                        $arrivalAt = isset($arrival['at']) ? new \DateTime($arrival['at']) : null;
    
                        $airline = \App\Models\Airline::where('iata_code', $carrierCode)->first();
                        $logo = $airline
                            ? 'https://logo.clearbit.com/' . $airline->domain . '?size=150'
                            : ($carrierCode ? 'https://via.placeholder.com/100?text=' . $carrierCode : null);
    
                        $offerFlight->flightLegs()->create([
                            'direction' => 'outbound',
                            'flight_number' => $flightNumber,
                            'departure_city' => $departure['iataCode'] ?? null,
                            'arrival_city' => $arrival['iataCode'] ?? null,
                            'departure_date' => $departureAt?->format('Y-m-d'),
                            'departure_time' => $departureAt?->format('H:i:s'),
                            'arrival_date' => $arrivalAt?->format('Y-m-d'),
                            'arrival_time' => $arrivalAt?->format('H:i:s'),
                            'carrier_code' => $carrierCode,
                            'airline_logo' => $logo,
                        ]);
                    }
                }
            }
        }
    
        // Retours
        $returnGroups = $flightResultsHtml['return_flights'] ?? [];
        foreach ($returnGroups as $group) {
            foreach ($group as $offer) {
                $itineraries = $offer['itineraries'] ?? [];
                foreach ($itineraries as $itinerary) {
                    foreach ($itinerary['segments'] as $segment) {
                        $carrierCode = $segment['carrierCode'] ?? null;
                        $flightNumber = $segment['number'] ?? null;
                        $departure = $segment['departure'] ?? [];
                        $arrival = $segment['arrival'] ?? [];
    
                        $departureAt = isset($departure['at']) ? new \DateTime($departure['at']) : null;
                        $arrivalAt = isset($arrival['at']) ? new \DateTime($arrival['at']) : null;
    
                        $airline = \App\Models\Airline::where('iata_code', $carrierCode)->first();
                        $logo = $airline
                            ? 'https://logo.clearbit.com/' . $airline->domain . '?size=150'
                            : ($carrierCode ? 'https://via.placeholder.com/100?text=' . $carrierCode : null);
    
                        $offerFlight->flightLegs()->create([
                            'direction' => 'return',
                            'flight_number' => $flightNumber,
                            'departure_city' => $departure['iataCode'] ?? null,
                            'arrival_city' => $arrival['iataCode'] ?? null,
                            'departure_date' => $departureAt?->format('Y-m-d'),
                            'departure_time' => $departureAt?->format('H:i:s'),
                            'arrival_date' => $arrivalAt?->format('Y-m-d'),
                            'arrival_time' => $arrivalAt?->format('H:i:s'),
                            'carrier_code' => $carrierCode,
                            'airline_logo' => $logo,
                        ]);
                    }
                }
            }
        }
    }
    

    // DIRECT_MULTIPLE / FLIGHT_SINGLE
    if ($flight_type === 'direct_single') {
        $offerFlight = $offre->offerFlights()->create([
            'places' => $flightResult->places ?? null,
            'price_adult' => $flightResult->price_adult ?? null,
            'price_child' => $flightResult->price_child ?? null,
            'price_baby' => $flightResult->price_baby ?? null,
            'flight_type' => $flight_type,
        ]);
    
        // CORRECTION ICI
        $offers = $flightResultsHtml; // Supposé être déjà data[0]['data']
    
        foreach ($offers as $offer) {
            $itineraries = $offer['itineraries'] ?? [];
            foreach ($itineraries as $index => $itinerary) {
                $direction = $index === 0 ? 'outbound' : 'return';
    
                foreach ($itinerary['segments'] as $segment) {
                    $carrierCode = $segment['carrierCode'] ?? null;
                    $flightNumber = $segment['number'] ?? null;
                    $departure = $segment['departure'] ?? [];
                    $arrival = $segment['arrival'] ?? [];
    
                    $departureAt = isset($departure['at']) ? new \DateTime($departure['at']) : null;
                    $arrivalAt = isset($arrival['at']) ? new \DateTime($arrival['at']) : null;
    
                    $airline = \App\Models\Airline::where('iata_code', $carrierCode)->first();
                    $logo = $airline
                        ? 'https://logo.clearbit.com/' . $airline->domain . '?size=150'
                        : ($carrierCode ? 'https://via.placeholder.com/100?text=' . $carrierCode : null);
    
                    $offerFlight->flightLegs()->create([
                        'direction' => $direction,
                        'flight_number' => $flightNumber,
                        'departure_city' => $departure['iataCode'] ?? null,
                        'arrival_city' => $arrival['iataCode'] ?? null,
                        'departure_date' => $departureAt ? $departureAt->format('Y-m-d') : null,
                        'departure_time' => $departureAt ? $departureAt->format('H:i:s') : null,
                        'arrival_date' => $arrivalAt ? $arrivalAt->format('Y-m-d') : null,
                        'arrival_time' => $arrivalAt ? $arrivalAt->format('H:i:s') : null,
                        'carrier_code' => $carrierCode,
                        'airline_logo' => $logo,
                    ]);
                }
            }
        }
    }
    
    
    // MULTI_FLIGHT_SINGLE
    elseif ($flight_type === 'multi_flight_single') {
        $offers = $searchParams['offers'] ?? [];
        foreach ($offers as $key => $offerParams) {
            $offerFlight = $offre->offerFlights()->create([
                'places' => $offerParams['places'] ?? null,
                'price_adult' => $offerParams['price_adult'] ?? null,
                'price_child' => $offerParams['price_child'] ?? null,
                'price_baby' => $offerParams['price_baby'] ?? null,
                'flight_type' => $flight_type,
            ]);
            // Allers
            foreach (($flightResultsHtml[$key]['outbound'] ?? []) as $flightId => $flight) {
                $segment = $flight['itineraries'][0]['segments'][0];
                $carrierCode = $segment['carrierCode'] ?? $segment['carrier_code'] ?? null;
                $airline = \App\Models\Airline::where('iata_code', $carrierCode)->first();
                $logo = $airline
                    ? 'https://logo.clearbit.com/'.$airline->domain.'?size=150'
                    : ($carrierCode ? 'https://via.placeholder.com/100?text='.$carrierCode : null);

                $offerFlight->flightLegs()->create([
                    'direction' => 'outbound',
                    'flight_number' => $segment['number'] ?? null,
                    'departure_city' => $segment['departure']['iataCode'] ?? null,
                    'arrival_city' => $segment['arrival']['iataCode'] ?? null,
                    'departure_date' => !empty($segment['departure']['at']) ? date('Y-m-d', strtotime($segment['departure']['at'])) : null,
                    'departure_time' => !empty($segment['departure']['at']) ? date('H:i:s', strtotime($segment['departure']['at'])) : null,
                    'arrival_date' => !empty($segment['arrival']['at']) ? date('Y-m-d', strtotime($segment['arrival']['at'])) : null,
                    'arrival_time' => !empty($segment['arrival']['at']) ? date('H:i:s', strtotime($segment['arrival']['at'])) : null,
                    'carrier_code' => $carrierCode,
                    'airline_logo' => $logo,
                ]);
            }
            // Retours
            foreach (($flightResultsHtml[$key]['return'] ?? []) as $flightId => $flight) {
                $segment = $flight['itineraries'][0]['segments'][0];
                $carrierCode = $segment['carrierCode'] ?? $segment['carrier_code'] ?? null;
                $airline = \App\Models\Airline::where('iata_code', $carrierCode)->first();
                $logo = $airline
                    ? 'https://logo.clearbit.com/'.$airline->domain.'?size=150'
                    : ($carrierCode ? 'https://via.placeholder.com/100?text='.$carrierCode : null);

                $offerFlight->flightLegs()->create([
                    'direction' => 'return',
                    'flight_number' => $segment['number'] ?? null,
                    'departure_city' => $segment['departure']['iataCode'] ?? null,
                    'arrival_city' => $segment['arrival']['iataCode'] ?? null,
                    'departure_date' => !empty($segment['departure']['at']) ? date('Y-m-d', strtotime($segment['departure']['at'])) : null,
                    'departure_time' => !empty($segment['departure']['at']) ? date('H:i:s', strtotime($segment['departure']['at'])) : null,
                    'arrival_date' => !empty($segment['arrival']['at']) ? date('Y-m-d', strtotime($segment['arrival']['at'])) : null,
                    'arrival_time' => !empty($segment['arrival']['at']) ? date('H:i:s', strtotime($segment['arrival']['at'])) : null,
                    'carrier_code' => $carrierCode,
                    'airline_logo' => $logo,
                ]);
            }
        }
    }
    // MULTI_FLIGHT_MULTIPLE
    elseif ($flight_type === 'multi_flight_multiple') {
        $offers = $searchParams['offers'] ?? [];
        foreach ($offers as $key => $offerParams) {
            $offerFlight = $offre->offerFlights()->create([
                'places' => $offerParams['general']['seats'] ?? null,
                'price_adult' => $offerParams['general']['price_adult'] ?? null,
                'price_child' => $offerParams['general']['price_child'] ?? null,
                'price_baby' => $offerParams['general']['price_baby'] ?? null,
                'flight_type' => $flight_type,
            ]);
            // Allers
            foreach (($flightResultsHtml['offers'][$key]['outbound'] ?? []) as $flightObj) {
                foreach ($flightObj as $flight) {
                    $segment = $flight['itineraries'][0]['segments'][0];
                    $carrierCode = $segment['carrierCode'] ?? $segment['carrier_code'] ?? null;
                    $airline = \App\Models\Airline::where('iata_code', $carrierCode)->first();
                    $logo = $airline
                        ? 'https://logo.clearbit.com/'.$airline->domain.'?size=150'
                        : ($carrierCode ? 'https://via.placeholder.com/100?text='.$carrierCode : null);

                    $offerFlight->flightLegs()->create([
                        'direction' => 'outbound',
                        'flight_number' => $segment['flightNumber'] ?? $segment['number'] ?? null,
                        'departure_city' => $segment['departure']['iataCode'] ?? null,
                        'arrival_city' => $segment['arrival']['iataCode'] ?? null,
                        'departure_date' => !empty($segment['departure']['at']) ? date('Y-m-d', strtotime($segment['departure']['at'])) : null,
                        'departure_time' => !empty($segment['departure']['at']) ? date('H:i:s', strtotime($segment['departure']['at'])) : null,
                        'arrival_date' => !empty($segment['arrival']['at']) ? date('Y-m-d', strtotime($segment['arrival']['at'])) : null,
                        'arrival_time' => !empty($segment['arrival']['at']) ? date('H:i:s', strtotime($segment['arrival']['at'])) : null,
                        'carrier_code' => $carrierCode,
                        'airline_logo' => $logo,
                    ]);
                }
            }
            // Retours
            foreach (($flightResultsHtml['offers'][$key]['return'] ?? []) as $flightObj) {
                foreach ($flightObj as $flight) {
                    $segment = $flight['itineraries'][0]['segments'][0];
                    $carrierCode = $segment['carrierCode'] ?? $segment['carrier_code'] ?? null;
                    $airline = \App\Models\Airline::where('iata_code', $carrierCode)->first();
                    $logo = $airline
                        ? 'https://logo.clearbit.com/'.$airline->domain.'?size=150'
                        : ($carrierCode ? 'https://via.placeholder.com/100?text='.$carrierCode : null);

                    $offerFlight->flightLegs()->create([
                        'direction' => 'return',
                        'flight_number' => $segment['flightNumber'] ?? $segment['number'] ?? null,
                        'departure_city' => $segment['departure']['iataCode'] ?? null,
                        'arrival_city' => $segment['arrival']['iataCode'] ?? null,
                        'departure_date' => !empty($segment['departure']['at']) ? date('Y-m-d', strtotime($segment['departure']['at'])) : null,
                        'departure_time' => !empty($segment['departure']['at']) ? date('H:i:s', strtotime($segment['departure']['at'])) : null,
                        'arrival_date' => !empty($segment['arrival']['at']) ? date('Y-m-d', strtotime($segment['arrival']['at'])) : null,
                        'arrival_time' => !empty($segment['arrival']['at']) ? date('H:i:s', strtotime($segment['arrival']['at'])) : null,
                        'carrier_code' => $carrierCode,
                        'airline_logo' => $logo,
                    ]);
                }
            }
        }
    }

    // 9. Event + redirect
    event(new \App\Events\OfferConfirmed([
        'input' => $input,
        'services' => $services,
        'booking' => $booking,
        'user' => auth()->user(),
        'offre_id' => $offre_id,
    ]));

    return redirect()->action([OfferController::class, 'create'])
    ->with('success', 'Offre créée avec succès .');    }
 
    
    public function services()
    {
        $input = session('input_hotel');
        return view('hotels.services', compact('input'));
    }

    public function detailRefus($offreId)
{
    $offre = \App\Models\Offre::with(['offerFlights.flightLegs'])
    ->where('id', $offreId)
    ->where('user_id', auth()->id())
    ->first();

    if (!$offre) {
        abort(404, "Offre non trouvée ou accès interdit.");
    }

    // Récupère l'objet HotelScraping si besoin pour détails de l'hôtel
    $hotelScraping = HotelScraping::find($offre->hotel_scraping_id);

    return view('hotels.detail_refus', [
        'offre' => $offre,
        'hotelScraping' => $hotelScraping,
        'offerFlights' => $offre->offerFlights ?? [],
    ]);
}

public function showConfirmForm($offreId)
{
    // Récupère l'offre du client
    $offre = \App\Models\Offre::with(['offerFlights.flightLegs'])
        ->where('id', $offreId)
        ->where('user_id', auth()->id())
        ->first();

    if (!$offre) {
        abort(404, "Offre non trouvée ou accès interdit.");
    }

    // Récupère le scraping hôtel
    $hotelScraping = HotelScraping::find($offre->hotel_scraping_id);

    // Prépare les données pour l’alpine.js du blade confirmation
    $input = [
        'name' => $hotelScraping->hotel_name ?? '',
        'total_rooms' => $offre->total_rooms ?? '',
        'room_types' => json_decode($offre->room_types, true) ?? [],
    ];
    $services = json_decode($offre->service_ids, true) ?? [];
    $scraped = $hotelScraping ? $hotelScraping->toArray() : [];

    // Si tu stockes les services dans une table, récupère-les !
    if (!empty($services)) {
        $services = \DB::table('services')->whereIn('id', $services)->get()->map(function ($s) {
            return (array)$s;
        })->toArray();
    }
     // Statut scraping
     if (empty($scraped)) {
        $scrapingStatus = 'pending';
    } elseif (
        (empty($scraped['address']) || $scraped['address'] === 'Adresse non trouvée') ||
        (empty($scraped['rating']) || $scraped['rating'] === 'Note non trouvée') ||
        (empty($scraped['images']) || (is_array($scraped['images']) && count($scraped['images']) === 0))
    ) {
        $scrapingStatus = 'error';
    } else {
        $scrapingStatus = 'success';
    }

    return view('hotels.confirmation', [
        'input' => $input,
        'services' => $services,
        'scraped' => $scraped,
        'scrapingStatus' => $scrapingStatus,

    ]);
}
public function mesOffres()
{
    $offres = \DB::table('offres')
        ->where('user_id', auth()->id())
        ->orderByDesc('created_at')
        ->paginate(10);

    return view('hotels.mes_offres', compact('offres'));
}
public function detailValidee($offreId)
{
    $offre = \App\Models\Offre::with(['offerFlights.flightLegs'])
    ->where('id', $offreId)
    ->where('user_id', auth()->id())
    ->first();

    if (!$offre) {
        abort(404, "Offre non trouvée ou accès interdit.");
    }

    $hotelScraping = \App\Models\HotelScraping::find($offre->hotel_scraping_id);

    $types = is_array($offre->room_types)
        ? $offre->room_types
        : (json_decode($offre->room_types, true) ?? []);

    $services = [];
    $serviceIds = json_decode($offre->service_ids ?? '[]', true);
    if (!empty($serviceIds)) {
        $services = \DB::table('services')->whereIn('id', $serviceIds)->get()->toArray();
    }

    // Éléments scrappés
    $scraped = $hotelScraping ? $hotelScraping->toArray() : [];

    return view('hotels.detail_validee', [
        'offre' => $offre,
        'hotelScraping' => $hotelScraping,
        'types' => $types,
        'services' => $services,
        'scraped' => $scraped,
        'offerFlights' => $offre->offerFlights ?? [],

    ]);
}
public function duplicate($id)
{
    $offre = \DB::table('offres')->where('id', $id)->where('user_id', auth()->id())->first();
    if (!$offre) abort(404, "Offre non trouvée ou accès interdit.");

    // Dupliquer l'offre
    $nouveauId = \DB::table('offres')->insertGetId([
        'hotel_scraping_id' => $offre->hotel_scraping_id,
        'user_id'          => $offre->user_id,
        'total_rooms'      => $offre->total_rooms,
        'room_types'       => $offre->room_types,
        'service_ids'      => $offre->service_ids,
        'statut'           => 'brouillon',
        'created_at'       => now(),
        'updated_at'       => now(),
    ]);

    // Préparer les variables pour confirmation.blade.php
    $nouvelleOffre = \DB::table('offres')->where('id', $nouveauId)->first();
    $hotelScraping = \App\Models\HotelScraping::find($nouvelleOffre->hotel_scraping_id);

    $input = [
        'name' => $hotelScraping->hotel_name ?? '',
        'total_rooms' => $nouvelleOffre->total_rooms ?? '',
        'room_types' => json_decode($nouvelleOffre->room_types, true) ?? [],
    ];
    $services = json_decode($nouvelleOffre->service_ids, true) ?? [];
    $scraped = $hotelScraping ? $hotelScraping->toArray() : [];

    if (!empty($services)) {
        $services = \DB::table('services')->whereIn('id', $services)->get()->map(function ($s) {
            return (array)$s;
        })->toArray();
    }

    // Statut scraping
    if (empty($scraped)) {
        $scrapingStatus = 'pending';
    } elseif (
        (empty($scraped['address']) || $scraped['address'] === 'Adresse non trouvée') ||
        (empty($scraped['rating']) || $scraped['rating'] === 'Note non trouvée') ||
        (empty($scraped['images']) || (is_array($scraped['images']) && count($scraped['images']) === 0))
    ) {
        $scrapingStatus = 'error';
    } else {
        $scrapingStatus = 'success';
    }

    // Afficher la vue confirmation préremplie
    return view('hotels.confirmation', [
        'input' => $input,
        'services' => $services,
        'scraped' => $scraped,
        'scrapingStatus' => $scrapingStatus,
    ]);
}

public function edit($id)
{
    $offre = \DB::table('offres')->where('id', $id)->where('user_id', auth()->id())->first();
    if (!$offre) abort(404, "Offre non trouvée ou accès interdit.");
    if ($offre->statut !== 'brouillon') abort(403, "Seules les offres en brouillon sont modifiables.");

    $hotelScraping = \App\Models\HotelScraping::find($offre->hotel_scraping_id);
    $input = [
        'name'        => $hotelScraping->hotel_name ?? '',
        'total_rooms' => $offre->total_rooms ?? '',
        'room_types'  => json_decode($offre->room_types, true) ?? [],
    ];
    $services = json_decode($offre->service_ids, true) ?? [];
    if (!empty($services)) {
        $services = \DB::table('services')->whereIn('id', $services)->get()->map(function ($s) {
            return (array)$s;
        })->toArray();
    }
    $scraped = $hotelScraping ? $hotelScraping->toArray() : [];

    // Statut scraping
    if (empty($scraped)) {
        $scrapingStatus = 'pending';
    } elseif (
        (empty($scraped['address']) || $scraped['address'] === 'Adresse non trouvée') ||
        (empty($scraped['rating']) || $scraped['rating'] === 'Note non trouvée') ||
        (empty($scraped['images']) || (is_array($scraped['images']) && count($scraped['images']) === 0))
    ) {
        $scrapingStatus = 'error';
    } else {
        $scrapingStatus = 'success';
    }

    return view('hotels.confirmation', [
        'input'          => $input,
        'services'       => $services,
        'scraped'        => $scraped,
        'scrapingStatus' => $scrapingStatus,
        'editOffreId'    => $id // Très important pour le form !
    ]);
}

public function update(Request $request, $id)
{
    // Enregistrer la modification d'une offre brouillon
    $offre = \DB::table('offres')->where('id', $id)->where('user_id', auth()->id())->first();
    if (!$offre) abort(404, "Offre non trouvée ou accès interdit.");
    if ($offre->statut !== 'brouillon') abort(403, "Seules les offres en brouillon sont modifiables.");

    // Ici, mets tes vérifications et l'update comme dans store/confirmStore...
    \DB::table('offres')->where('id', $id)->update([
        'total_rooms' => $request->total_rooms,
        'room_types'  => json_encode($request->room_types),
        'service_ids' => json_encode($request->service_ids),
        'updated_at'  => now(),
    ]);
    return redirect()->route('mes.offres')->with('success', 'Offre modifiée !');
}

public function archive($id)
{
    $offre = \DB::table('offres')->where('id', $id)->where('user_id', auth()->id())->first();
    if (!$offre) abort(404, "Offre non trouvée ou accès interdit.");
    // Statut archivé
    \DB::table('offres')->where('id', $id)->update([
        'statut' => 'archivee',
        'updated_at' => now(),
    ]);
    return redirect()->route('mes.offres')->with('success', "Offre archivée !");
}

public function stop($id)
{
    $offre = \DB::table('offres')->where('id', $id)->where('user_id', auth()->id())->first();
    if (!$offre) abort(404, "Offre non trouvée ou accès interdit.");
    if ($offre->statut !== 'valide') abort(403, "Seules les offres validées peuvent être arrêtées.");
    \DB::table('offres')->where('id', $id)->update([
        'statut' => 'arretee',
        'updated_at' => now(),
    ]);
    return redirect()->route('mes.offres')->with('success', "Offre arrêtée !");
}

public function updateConfirmation(Request $request, $id)
{
    $offre = \DB::table('offres')
        ->where('id', $id)
        ->where('user_id', auth()->id())
        ->first();
    if (!$offre) abort(404, "Offre non trouvée ou accès interdit.");
    if ($offre->statut !== 'brouillon') abort(403, "Seules les offres en brouillon sont modifiables.");

    $input = json_decode($request->input('input'), true);
    $services = json_decode($request->input('services'), true);
    $booking = json_decode($request->input('booking'), true);

    // Mettre à jour le scraping si besoin
    $hotelScraping = \App\Models\HotelScraping::find($offre->hotel_scraping_id);
    if ($hotelScraping && is_array($booking)) {
        $hotelScraping->address = $booking['address'] ?? $hotelScraping->address;
        $hotelScraping->rating = $booking['rating'] ?? $hotelScraping->rating;
        $hotelScraping->images = $booking['images'] ?? $hotelScraping->images;
        $hotelScraping->save();
    }

    // Calcul statut scraping
    $scraped = $hotelScraping ? $hotelScraping->toArray() : [];
    if (empty($scraped)) {
        $scrapingStatus = 'pending';
    } elseif (
        (empty($scraped['address']) || $scraped['address'] === 'Adresse non trouvée') ||
        (empty($scraped['rating']) || $scraped['rating'] === 'Note non trouvée') ||
        (empty($scraped['images']) || (is_array($scraped['images']) && count($scraped['images']) === 0))
    ) {
        $scrapingStatus = 'error';
    } else {
        $scrapingStatus = 'success';
    }

    // ======= VÉRIFICATIONS MÉTIERS CHAMBRES =======
    $totalRooms = isset($input['total_rooms']) ? (int)$input['total_rooms'] : 0;
    $sumAvailable = 0;
    $roomTypes = $input['room_types'] ?? [];

    foreach ($roomTypes as $room) {
        $sumAvailable += (int) ($room['available_rooms'] ?? 0);
    }
    if ($sumAvailable !== $totalRooms) {
        return view('hotels.confirmation', [
            'input' => $input,
            'services' => $services,
            'scraped' => $scraped,
            'scrapingStatus' => $scrapingStatus,
            'editOffreId' => $id
        ])->withErrors([
            'room_types' => 'La somme des chambres en vente doit être exactement égale au nombre total de chambres (' . $totalRooms . ').'
        ]);
    }

    foreach ($roomTypes as $room) {
        $type = strtolower($room['type'] ?? '');
        $adults = (int) ($room['adults'] ?? 0);
        $children = (int) ($room['children'] ?? 0);
        $kids = (int) ($room['kids'] ?? 0);
        $babies = (int) ($room['babies'] ?? 0);
        $totalMinors = $children + $kids + $babies;

        if ($type === 'single' && ($adults > 1 || $totalMinors > 3)) {
            return view('hotels.confirmation', [
                'input' => $input,
                'services' => $services,
                'scraped' => $scraped,
                'scrapingStatus' => $scrapingStatus,
                'editOffreId' => $id
            ])->withErrors([
                'room_types' => 'Chambre SINGLE : max 1 adulte et 3 mineurs (enfant/kid/bébé).'
            ]);
        }
        if ($type === 'double' && ($adults > 2 || $totalMinors > 2)) {
            return view('hotels.confirmation', [
                'input' => $input,
                'services' => $services,
                'scraped' => $scraped,
                'scrapingStatus' => $scrapingStatus,
                'editOffreId' => $id
            ])->withErrors([
                'room_types' => 'Chambre DOUBLE : max 2 adultes et 2 mineurs (enfant/kid/bébé).'
            ]);
        }
        if ($type === 'triple' && ($adults > 3 || $babies > 1 || ($children + $kids) > 0)) {
            return view('hotels.confirmation', [
                'input' => $input,
                'services' => $services,
                'scraped' => $scraped,
                'scrapingStatus' => $scrapingStatus,
                'editOffreId' => $id
            ])->withErrors([
                'room_types' => 'Chambre TRIPLE : max 3 adultes et 1 bébé seulement (pas d’enfant ou kid).'
            ]);
        }
    }
    // ======= FIN VÉRIFICATIONS MÉTIERS =======

    // Mise à jour des services (nouveaux ou existants)
    $serviceIds = [];
    if (is_array($services)) {
        foreach ($services as $service) {
            if (isset($service['id'])) {
                $serviceIds[] = $service['id'];
            } else {
                // Nouveau service à insérer
                $idService = \DB::table('services')->insertGetId([
                    'type_service' => $service['type_service'] ?? null,
                    'date_service' => $service['date_service'] ?? null,
                    'description'  => $service['description'] ?? null,
                    'prix'         => $service['prix'] ?? null,
                    'capacite'     => $service['capacite'] ?? null,
                    'type'         => $service['type'] ?? null,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
                $serviceIds[] = $idService;
            }
        }
    }

    // Mise à jour de l'offre
    \DB::table('offres')->where('id', $id)->update([
        'total_rooms' => $input['total_rooms'],
        'room_types'  => json_encode($input['room_types']),
        'service_ids' => json_encode($serviceIds),
        'updated_at'  => now(),
    ]);

    return redirect()->route('mes.offres')->with('success', 'Offre modifiée !');
}

public function updateVol(Request $request, FlightSearch $flightSearch)
    {
        // Validation des données envoyées
        $validated = $request->validate([
            'results_json' => 'required|string',
        ]);

        // Décodage pour vérifier que c’est bien du JSON valide
        $decoded = json_decode($validated['results_json'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return back()->withErrors(['results_json' => 'Le JSON envoyé est invalide.']);
        }

        // Sauvegarde dans la colonne results
        $flightSearch->results = json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $flightSearch->save();

        return back()->with('success', 'Les informations du vol ont été mises à jour avec succès.');
    }

}
