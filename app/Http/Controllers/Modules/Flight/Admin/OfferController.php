<?php

namespace App\Http\Controllers\Modules\Flight\Admin;

use Illuminate\Http\Request;
use Modules\AdminController;
use App\Models\Offer;
use App\Services\AmadeusFlightService;
use Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Jobs\SearchFlightInfoJob;
use Illuminate\Support\Facades\Cache;
use App\Models\FlightSearch;

use App\Jobs\ProcessDirectMultipleOffer;
use App\Jobs\SearchMultiFlightSingleInfoJob;
use App\Jobs\SearchMultiFlightMultipleJob;
use Illuminate\Support\Facades\Session;


class OfferController extends AdminController
{
    protected $amadeusService;
    public function __construct(AmadeusFlightService $amadeusService)
    {
        $this->setActiveMenu(route('flight.admin.offers.index'));
        $this->amadeusService = $amadeusService;
    }
    public function index(Request $request)
    {
        $query = Offer::query()->with(['flights', 'author']);
        
        $data = [
            'rows' => $query->paginate(20),
            'breadcrumbs' => [
                ['name' => __('Flights'), 'url' => route('flight.admin.index')],
                ['name' => __('Offers'), 'class' => 'active']
            ],
            'page_title' => __("Flight Offers Management")
        ];
        
        return view('Flight::admin.offer.index', $data);
    }
    public function create(Request $request)
    {
        Session::forget('offer_created');
    Session::forget('offer_id');
        return response()
            ->view('Flight::admin.offer.create', [
                'row' => new Offer(),
                'breadcrumbs' => [
                    ['name' => 'Flights', 'url' => '#'],
                    ['name' => 'Offers', 'url' => '#'],
                    ['name' => 'Create Offer', 'class' => 'active']
                ],
                'page_title' => "Create New Flight Offer"
            ])
            ->header('Content-Type', 'text/html');
    }
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'type' => 'required|in:direct_single,direct_multiple,multi_flight_single,multi_flight_multiple',
            ]);
    
            Log::info('Creating offer', ['type' => $request->type, 'user' => auth()->id()]);
    
            if ($request->type === 'direct_single') {
                return $this->storeDirectSingleOffer($request);
            }
    
            if ($request->type === 'direct_multiple') {
                return $this->storeDirectMultipleOffer($request);
            }
            if ($request->type === 'multi_flight_single') {
                return $this->storeMultiFlightSingleOffer($request);
            }
            
            if ($request->type === 'multi_flight_multiple') {
                return $this->storeMultiFlightMultipleOffer($request);
            }

          
    
            

        
            throw new \Exception("Type d'offre non géré");
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error', ['errors' => $e->errors()]);
            return response()->json([
                'status' => 'error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Offer creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->except(['_token'])
            ]);
    
            return response()->json([
                'status' => 'error',
                'message' => 'Une erreur est survenue: ' . $e->getMessage()
            ], 500);
        }
    }
    protected function storeDirectSingleOffer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'included_airline_codes' => 'nullable|string|regex:/^[A-Za-z]{2}(,[A-Za-z]{2})*$/',
            'departure_date' => 'required|date|after_or_equal:today',
            'departure_city' => 'required|string|size:3|regex:/^[A-Za-z]{3}$/',
            'arrival_city' => 'required|string|size:3|regex:/^[A-Za-z]{3}$/',
            'return_date' => 'nullable|date|after_or_equal:departure_date',
            'travel_class' => 'nullable|in:ECONOMY,PREMIUM_ECONOMY,BUSINESS,FIRST',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $search = FlightSearch::create([
            'user_id' => auth()->id(),
            'search_params' => $request->all(),
            'status' => 'pending',
            'places' => $request->input('places'),
            'price_adult' => $request->input('price_adult'),
            'price_child' => $request->input('price_child'),
            'price_baby' => $request->input('price_baby'),
            'type' => 'direct_single',
        ]);
        
        // Passe l'ID au job
        SearchFlightInfoJob::dispatch($search->id);
        Session::put('offer_created', true);
        return redirect()->route('hotels.create')->with('message', 'Recherche lancée avec succès.');

    }

    public function searchFlightInfo($offer, $request = null)
    {
        try {
            $request = (object) ($request ?? request());
            $results = [];
    
            $flightNumberFilter = !empty($request->flight_number) ? substr($request->flight_number, 2) : null;
            $returnFlightNumberFilter = !empty($request->return_flight_number) ? substr($request->return_flight_number, 2) : null;
            $isTK = isset($request->flight_number) && strtoupper(substr($request->flight_number, 0, 2)) === 'TK';
    
            if ($isTK) {
                $duffelService = new \App\Services\DuffelFlightService();
                $rawDuffelResults = $duffelService->searchFlightOffers((array)$request);
                $formattedResults = $duffelService->formatDuffelResults($rawDuffelResults);
                $formattedResults = $duffelService->filterUniqueDuffelOffers($formattedResults);

                // 🔍 Filtrer par numéro de vol (aller)
                if ($flightNumberFilter) {
                    $formattedResults = array_filter($formattedResults, function ($offer) use ($flightNumberFilter) {
                        foreach ($offer['itineraries'] as $itinerary) {
                            foreach ($itinerary['segments'] as $segment) {
                                if ($segment['number'] === $flightNumberFilter) {
                                    return true;
                                }
                            }
                        }
                        return false;
                    });
                }
    
                // 🔍 Filtrer retour si applicable
                if ($returnFlightNumberFilter) {
                    $formattedResults = array_filter($formattedResults, function ($offer) use ($returnFlightNumberFilter) {
                        foreach ($offer['itineraries'] as $itinerary) {
                            foreach ($itinerary['segments'] as $segment) {
                                if ($segment['number'] === $returnFlightNumberFilter) {
                                    return true;
                                }
                            }
                        }
                        return false;
                    });
                }
    
                $results[] = ['source' => 'duffel', 'data' => array_values($formattedResults)];
            } else {
                $params = [
                    'currencyCode' => 'MAD',
                    'sources' => ['GDS'],
                    'searchCriteria' => ['flightFilters' => ['maxConnections' => 0]],
                    'originDestinations' => [[
                        'id' => '1',
                        'originLocationCode' => strtoupper($request->departure_city ?? $offer->flights->first()->departure_city),
                        'destinationLocationCode' => strtoupper($request->arrival_city ?? $offer->flights->first()->arrival_city),
                        'departureDateTimeRange' => ['date' => $request->departure_date ?? $offer->flights->first()->departure_date->format('Y-m-d')]
                    ]],
                    'travelers' => [['id' => '1', 'travelerType' => 'ADULT']]
                ];
    
                if (!empty($request->return_departure_city) && !empty($request->return_arrival_city) && !empty($request->return_date)) {
                    $params['originDestinations'][] = [
                        'id' => '2',
                        'originLocationCode' => strtoupper($request->return_departure_city),
                        'destinationLocationCode' => strtoupper($request->return_arrival_city),
                        'departureDateTimeRange' => ['date' => $request->return_date]
                    ];
                }
    
                $flightData = $this->amadeusService->searchFlightOffers($params, true);
    
                if (!isset($flightData['data']) || empty($flightData['data'])) {
                    throw new \Exception("Aucun vol trouvé via Amadeus.");
                }
    
                if ($flightNumberFilter) {
                    $flightData['data'] = array_filter($flightData['data'], function ($offer) use ($flightNumberFilter) {
                        foreach ($offer['itineraries'] as $itinerary) {
                            foreach ($itinerary['segments'] as $segment) {
                                if ($segment['number'] === $flightNumberFilter) {
                                    return true;
                                }
                            }
                        }
                        return false;
                    });
                }
    
                if ($returnFlightNumberFilter) {
                    $flightData['data'] = array_filter($flightData['data'], function ($offer) use ($returnFlightNumberFilter) {
                        foreach ($offer['itineraries'] as $itinerary) {
                            foreach ($itinerary['segments'] as $segment) {
                                if ($segment['number'] === $returnFlightNumberFilter) {
                                    return true;
                                }
                            }
                        }
                        return false;
                    });
                }
    
                $results[] = ['source' => 'amadeus', 'data' => array_values($flightData['data'])];
            }
    
            return [
                'status' => 'success',
                'data' => $results,
            ];
    
        } catch (\Exception $e) {
            \Log::error('Flight search error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
    
            return [
                'status' => 'error',
                'message' => 'Erreur API: ' . $e->getMessage()
            ];
        }
    }
    
    
    
    
    public function checkDirectMultipleResults($token)
    {
        $data = Cache::get("direct_multiple_{$token}");
    
        if (!$data) {
            return response()->json(['status' => 'pending']);
        }
    
        return response()->json($data);
    }
    


    /**
     * Store a new direct multiple offer.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    protected function storeDirectMultipleOffer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'flights' => 'required|array|min:1',
            'flights.*.departure_city' => 'required|string|size:3|regex:/^[A-Za-z]{3}$/',
            'flights.*.arrival_city' => 'required|string|size:3|regex:/^[A-Za-z]{3}$/',
            'flights.*.departure_date' => 'required|date|after_or_equal:today',
            'flights.*.flight_number' => 'required|string|regex:/^[A-Za-z]{2}[0-9]{1,4}$/',
            'return_flights' => 'nullable|array|min:1',
            'return_flights.*.departure_city' => 'required|string|size:3|regex:/^[A-Za-z]{3}$/',
            'return_flights.*.arrival_city' => 'required|string|size:3|regex:/^[A-Za-z]{3}$/',
            'return_flights.*.departure_date' => 'required|date|after_or_equal:today',
            'return_flights.*.flight_number' => 'required|string|regex:/^[A-Za-z]{2}[0-9]{1,4}$/',
        ], [
            'flights.*.departure_city.regex' => 'Le code aéroport doit contenir 3 lettres',
            'flights.*.arrival_city.regex' => 'Le code aéroport doit contenir 3 lettres',
            'flights.*.flight_number.regex' => 'Le numéro de vol doit contenir 2 lettres suivies de 1 à 4 chiffres',
            'return_flights.*.departure_city.regex' => 'Le code aéroport doit contenir 3 lettres',
            'return_flights.*.arrival_city.regex' => 'Le code aéroport doit contenir 3 lettres',
            'return_flights.*.flight_number.regex' => 'Le numéro de vol doit contenir 2 lettres suivies de 1 à 4 chiffres',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }
    
        try {
            $flights = collect($request->flights)->map(function ($flight) {
                return (object) [
                    'departure_city' => strtoupper($flight['departure_city']),
                    'arrival_city' => strtoupper($flight['arrival_city']),
                    'departure_date' => \Carbon\Carbon::parse($flight['departure_date']),
                    'flight_number' => strtoupper($flight['flight_number']),
                ];
            });
    
            $returnFlights = collect($request->return_flights)->map(function ($flight) {
                return (object) [
                    'departure_city' => strtoupper($flight['departure_city']),
                    'arrival_city' => strtoupper($flight['arrival_city']),
                    'departure_date' => \Carbon\Carbon::parse($flight['departure_date']),
                    'flight_number' => strtoupper($flight['flight_number']),
                ];
            });
    
            $offer = new \stdClass();
            $offer->flights = $flights;
            $offer->return_flights = $returnFlights;
            $offer->flight_type = 'direct_multiple';

            

            dispatch(new ProcessDirectMultipleOffer( $offer ,   $request->input('places'),
            $request->input('price_adult'),
            $request->input('price_child'),
            
            $request->input('price_baby'),Auth::id(), $offer->flight_type
            
        ));
           
        Session::put('offer_created', true);
            return response()->json([
                'status' => 'processing',
                
                'message' => 'Recherche en cours...'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function searchFlightInfoForMultipleFlights($offer)
    {
        try {
            $results = [];
    
            // Traitement des vols aller
            foreach ($offer->flights as $flight) {
                $params = [
                    'currencyCode' => 'MAD',
                    'sources' => ['GDS'],
                    'originDestinations' => [
                        [
                            'id' => '1',
                            'originLocationCode' => $flight->departure_city,
                            'destinationLocationCode' => $flight->arrival_city,
                            'departureDateTimeRange' => [
                                'date' => $flight->departure_date->format('Y-m-d'),
                            ]
                        ]
                    ],
                    'travelers' => [
                        [
                            'id' => '1',
                            'travelerType' => 'ADULT'
                        ]
                    ],
                    'searchCriteria' => [
                        'flightFilters' => [
                            'maxConnections' => 0,
                        ]
                    ]
                ];
    
                $airlineCode = strtoupper(substr($flight->flight_number, 0, 2));
    
                if ($airlineCode !== 'TK') {
                    $flightData = $this->amadeusService->searchFlightOffers($params, true);
                } else {
                    $duffelService = new \App\Services\DuffelFlightService();
                    $rawDuffelResults = $duffelService->searchFlightOffers((array)$flight);
                    $formattedResults = $duffelService->formatDuffelResults($rawDuffelResults);
                    $formattedResults = $duffelService->filterUniqueDuffelOffers($formattedResults);
                    $flightData = ['data' => $formattedResults];
                }
    
                if (!isset($flightData['data']) || empty($flightData['data'])) {
                    Log::warning("Aucun résultat trouvé pour le vol aller", [
                        'flight_number' => $flight->flight_number,
                        'params' => $params,
                        'response' => $flightData
                    ]);
                    continue;
                }
    
                $flightNumber = strtoupper($flight->flight_number); // Ex: "TK243"
    
                Log::info('🔍 Recherche du numéro de vol ' . $flightNumber, [
                    'offres' => $flightData['data']
                ]);
    
                $filteredData = array_filter($flightData['data'], function ($offer) use ($flightNumber) {
                    foreach ($offer['itineraries'] as $itinerary) {
                        foreach ($itinerary['segments'] as $segment) {
                            $segmentCode = strtoupper($segment['carrierCode'] . $segment['number']);
                            if ($segmentCode === $flightNumber) {
                                return true;
                            }
                        }
                    }
                    return false;
                });
    
                $results['flights'][] = $filteredData;
            }
    
            // Traitement des vols retour
            foreach ($offer->return_flights as $returnFlight) {
                $params = [
                    'currencyCode' => 'MAD',
                    'sources' => ['GDS'],
                    'originDestinations' => [
                        [
                            'id' => '1',
                            'originLocationCode' => $returnFlight->departure_city,
                            'destinationLocationCode' => $returnFlight->arrival_city,
                            'departureDateTimeRange' => [
                                'date' => $returnFlight->departure_date->format('Y-m-d'),
                            ]
                        ]
                    ],
                    'travelers' => [
                        [
                            'id' => '1',
                            'travelerType' => 'ADULT'
                        ]
                    ],
                    'searchCriteria' => [
                        'flightFilters' => [
                            'maxConnections' => 0,
                        ]
                    ]
                ];
    
                $airlineCode = strtoupper(substr($returnFlight->flight_number, 0, 2));
    
                if ($airlineCode !== 'TK') {
                    $flightData = $this->amadeusService->searchFlightOffers($params, true);
                } else {
                    $duffelService = new \App\Services\DuffelFlightService();
                    $rawDuffelResults = $duffelService->searchFlightOffers((array)$returnFlight);
                    $formattedResults = $duffelService->formatDuffelResults($rawDuffelResults);
                    $formattedResults = $duffelService->filterUniqueDuffelOffers($formattedResults);
                    $flightData = ['data' => $formattedResults];
                }
    
                if (!isset($flightData['data']) || empty($flightData['data'])) {
                    Log::warning("Aucun résultat trouvé pour le vol retour", [
                        'flight_number' => $returnFlight->flight_number,
                        'params' => $params,
                        'response' => $flightData
                    ]);
                    continue;
                }
    
                $flightNumber = strtoupper($returnFlight->flight_number);
    
                Log::info('🔍 Recherche du numéro de vol ' . $flightNumber, [
                    'offres' => $flightData['data']
                ]);
    
                $filteredData = array_filter($flightData['data'], function ($offer) use ($flightNumber) {
                    foreach ($offer['itineraries'] as $itinerary) {
                        foreach ($itinerary['segments'] as $segment) {
                            $segmentCode = strtoupper($segment['carrierCode'] . $segment['number']);
                            if ($segmentCode === $flightNumber) {
                                return true;
                            }
                        }
                    }
                    return false;
                });
    
                $results['return_flights'][] = $filteredData;
            }
    
            return [
                'status' => 'success',
                'data' => $results,
            ];
    
        } catch (\Exception $e) {
            Log::error('Amadeus API Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
    
            return [
                'status' => 'error',
                'message' => 'Erreur API: ' . $e->getMessage()
            ];
        }
    }
    

    /**
     * Store a new multi-flight single offer.
     *
     * @param Request $request
      */

    public function storeMultiFlightSingleOffer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'offers' => 'required|array|min:1',
            'offers.*.outbound.departure_city' => 'required|string|size:3|regex:/^[A-Za-z]{3}$/',
            'offers.*.outbound.arrival_city' => 'required|string|size:3|regex:/^[A-Za-z]{3}$/',
            'offers.*.outbound.departure_date' => 'required|date|after_or_equal:today',
            'offers.*.outbound.flight_number' => 'required|string|regex:/^[A-Za-z]{2}[0-9]{1,4}$/',
            'offers.*.return.departure_city' => 'nullable|string|size:3|regex:/^[A-Za-z]{3}$/',
            'offers.*.return.arrival_city' => 'nullable|string|size:3|regex:/^[A-Za-z]{3}$/',
            'offers.*.return.return_date' => 'nullable|date|after_or_equal:offers.*.outbound.departure_date',
            'offers.*.return.flight_number' => 'nullable|string|regex:/^[A-Za-z]{2}[0-9]{1,4}$/',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }
    
        $search = FlightSearch::create([
            'user_id' => auth()->id(),
            'search_params' => $request->all(),
            'status' => 'pending',
            'type' => 'multi_flight_single',
        ]);
    
        SearchMultiFlightSingleInfoJob::dispatch($search->id);
        Session::put('offer_created', true);
        return redirect()->route('hotels.create')->with('message', 'Recherche lancée avec succès.');

    }
    
    
    public function searchMultiFlightSingleInfo($offer)
    {
        try {
            $results = [];
    
            // === VOL ALLER ===
            $outboundFlightNumber = strtoupper($offer->flight_number);
            $airlineCode = substr($outboundFlightNumber, 0, 2);
    
            if ($airlineCode === 'TK') {
                $duffelService = new \App\Services\DuffelFlightService();
                $raw = $duffelService->searchFlightOffers((array) $offer);
                $formatted = $duffelService->formatDuffelResults($raw);
                $unique = $duffelService->filterUniqueDuffelOffers($formatted);
                $outboundResults = ['data' => $unique];
            } else {
                $params = [
                    'currencyCode' => 'MAD',
                    'sources' => ['GDS'],
                    'originDestinations' => [[
                        'id' => '1',
                        'originLocationCode' => $offer->departure_city,
                        'destinationLocationCode' => $offer->arrival_city,
                        'departureDateTimeRange' => ['date' => $offer->departure_date->format('Y-m-d')],
                    ]],
                    'travelers' => [['id' => '1', 'travelerType' => 'ADULT']],
                    'searchCriteria' => ['flightFilters' => ['maxConnections' => 0]],
                ];
                $outboundResults = $this->amadeusService->searchFlightOffers($params, true);
            }
    
            $results['outbound'] = [];
    
            foreach ($outboundResults['data'] ?? [] as $offerData) {
                foreach ($offerData['itineraries'] as $itinerary) {
                    foreach ($itinerary['segments'] as $segment) {
                        $code = strtoupper($segment['carrierCode'] . $segment['number']);
                        $from = strtoupper($segment['departure']['iataCode'] ?? '');
                        $to = strtoupper($segment['arrival']['iataCode'] ?? '');
                        if (
                            $code === $outboundFlightNumber &&
                            $from === strtoupper($offer->departure_city) &&
                            $to === strtoupper($offer->arrival_city)
                        ) {
                            $results['outbound'][] = [
                                'type' => $offerData['type'],
                                'id' => $offerData['id'],
                                'price' => $offerData['price'],
                                'validatingAirlineCodes' => $offerData['validatingAirlineCodes'],
                                'travelerPricings' => $offerData['travelerPricings'],
                                'itineraries' => [[ 'segments' => [$segment], 'duration' => $itinerary['duration'] ?? '' ]]
                            ];
                        }
                    }
                }
            }
    
            // === VOL RETOUR ===
            if (!empty($offer->return_departure_city) && !empty($offer->return_arrival_city) && !empty($offer->return_date)) {
                $returnFlightNumber = strtoupper($offer->return_flight_number);
                $returnAirlineCode = substr($returnFlightNumber, 0, 2);
    
                if ($returnAirlineCode === 'TK') {
                    $duffelService = new \App\Services\DuffelFlightService();
                    $raw = $duffelService->searchFlightOffers([
                        'departure_city' => $offer->return_departure_city,
                        'arrival_city' => $offer->return_arrival_city,
                        'departure_date' => $offer->return_date->format('Y-m-d'),
                        'travel_class' => 'ECONOMY'
                    ]);
                    $formatted = $duffelService->formatDuffelResults($raw);
                    $unique = $duffelService->filterUniqueDuffelOffers($formatted);
                    $returnResults = ['data' => $unique];
                } else {
                    $params = [
                        'currencyCode' => 'MAD',
                        'sources' => ['GDS'],
                        'originDestinations' => [[
                            'id' => '2',
                            'originLocationCode' => $offer->return_departure_city,
                            'destinationLocationCode' => $offer->return_arrival_city,
                            'departureDateTimeRange' => ['date' => $offer->return_date->format('Y-m-d')],
                        ]],
                        'travelers' => [['id' => '1', 'travelerType' => 'ADULT']],
                        'searchCriteria' => ['flightFilters' => ['maxConnections' => 0]],
                    ];
                    $returnResults = $this->amadeusService->searchFlightOffers($params, true);
                }
    
                $results['return'] = [];
    
                foreach ($returnResults['data'] ?? [] as $offerData) {
                    foreach ($offerData['itineraries'] as $itinerary) {
                        foreach ($itinerary['segments'] as $segment) {
                            $code = strtoupper($segment['carrierCode'] . $segment['number']);
                            $from = strtoupper($segment['departure']['iataCode'] ?? '');
                            $to = strtoupper($segment['arrival']['iataCode'] ?? '');
                            if (
                                $code === $returnFlightNumber &&
                                $from === strtoupper($offer->return_departure_city) &&
                                $to === strtoupper($offer->return_arrival_city)
                            ) {
                                $results['return'][] = [
                                    'type' => $offerData['type'],
                                    'id' => $offerData['id'],
                                    'price' => $offerData['price'],
                                    'validatingAirlineCodes' => $offerData['validatingAirlineCodes'],
                                    'travelerPricings' => $offerData['travelerPricings'],
                                    'itineraries' => [[ 'segments' => [$segment], 'duration' => $itinerary['duration'] ?? '' ]]
                                ];
                            }
                        }
                    }
                }
            }
    
            return ['status' => 'success', 'data' => $results];
    
        } catch (\Exception $e) {
            Log::error('❌ Erreur lors de la recherche multi flight single', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
    
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    

    public function getForm(Request $request)
    {
        $validTypes = ['direct_single', 'direct_multiple', 'multi_flight_single', 'multi_flight_multiple'];
        $type = $request->input('type');
    
        if (!in_array($type, $validTypes)) {
            Log::error("Type de formulaire invalide demandé", ['type' => $type]);
            return response()->json([
                'error' => 'Type de formulaire non valide',
                'valid_types' => $validTypes
            ], 400);
        }
    
        $viewPath = "Flight::admin.offer.forms.{$type}";
        
        if (!view()->exists($viewPath)) {
            Log::error("Vue de formulaire introuvable", ['path' => $viewPath]);
            return response()->json([
                'error' => 'Le formulaire demandé n\'existe pas',
                'view_path' => $viewPath
            ], 404);
        }
    
        try {
            return view($viewPath, [
                'current_type' => $type,
                'form_action' => route('flight.admin.offers.store')
            ]);
        } catch (\Exception $e) {
            Log::error("Erreur de rendu du formulaire", [
                'type' => $type,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'error' => 'Erreur lors du chargement du formulaire',
                'details' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Store a new multi-flight multiple offer.
     *
     * @param Request $request
     * 
     */


    protected function storeMultiFlightMultipleOffer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'offers' => 'required|array|min:1',
            'offers.*.general.seats' => 'required|integer|min:1',
            'offers.*.general.price_adult' => 'required|numeric|min:0',
            'offers.*.outbound' => 'required|array|min:1',
            'offers.*.outbound.*.departure_city' => 'required|string|size:3|regex:/^[A-Za-z]{3}$/',
            'offers.*.outbound.*.arrival_city' => 'required|string|size:3|regex:/^[A-Za-z]{3}$/',
            'offers.*.outbound.*.departure_date' => 'required|date|after_or_equal:today',
            'offers.*.outbound.*.flight_number' => 'required|string|regex:/^[A-Za-z]{2}[0-9]{1,4}$/',
            'offers.*.return' => 'required|array|min:1',
            'offers.*.return.*.departure_city' => 'required|string|size:3|regex:/^[A-Za-z]{3}$/',
            'offers.*.return.*.arrival_city' => 'required|string|size:3|regex:/^[A-Za-z]{3}$/',
            'offers.*.return.*.departure_date' => 'required|date|after_or_equal:today',
            'offers.*.return.*.flight_number' => 'required|string|regex:/^[A-Za-z]{2}[0-9]{1,4}$/',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $search = FlightSearch::create([
            'user_id' => auth()->id(),
            'search_params' => $request->all(),
            'status' => 'pending',
            'type' => 'multi_flight_multiple',
        ]);
    
        SearchMultiFlightMultipleJob::dispatch($search->id);
        Session::put('offer_created', true);
        return redirect()->route('hotels.create')->with('message', 'Recherche lancée avec succès.');
    
      
    }
    
    /**
     * Normalize a flight offer by extracting useful fields.
     *
     * @param array $flightOffer
     * @return array
     */
    public function normalizeFlightOffer($flightOffer)
    {
        $normalized = [
            'id' => $flightOffer['id'] ?? 'N/A',
            'price' => $flightOffer['price']['total'] ?? 'N/A',
            'currency' => $flightOffer['price']['currency'] ?? 'N/A',
            'itineraries' => [],
        ];
    
        foreach ($flightOffer['itineraries'] ?? [] as $itinerary) {
            $segments = [];
            foreach ($itinerary['segments'] ?? [] as $segment) {
                $segments[] = [
                    'carrierCode' => $segment['carrierCode'] ?? 'N/A',
                    'flightNumber' => $segment['number'] ?? 'N/A',
                    'departure' => [
                        'iataCode' => $segment['departure']['iataCode'] ?? 'N/A',
                        'at' => $segment['departure']['at'] ?? 'N/A',
                    ],
                    'arrival' => [
                        'iataCode' => $segment['arrival']['iataCode'] ?? 'N/A',
                        'at' => $segment['arrival']['at'] ?? 'N/A',
                    ],
                ];
            }
            $normalized['itineraries'][] = [
                'duration' => $itinerary['duration'] ?? 'N/A',
                'segments' => $segments,
            ];
        }
    
        return $normalized;
    }

/**
 * Search flight information for a single leg of a flight.
 *
 * @param array $flight
 * @return array
 */
public function searchMultiFlightMultipleInfo($flight)
{
    try {
        $flightNumberFull = strtoupper($flight['flight_number']); // e.g. TK0618
        $airlineCode = substr($flightNumberFull, 0, 2);
        $results = [];

        if ($airlineCode === 'TK') {
            // --- Utilisation de Duffel pour Turkish Airlines ---
            $duffelService = new \App\Services\DuffelFlightService();
            $rawResults = $duffelService->searchFlightOffers([
                'departure_city' => $flight['departure_city'],
                'arrival_city' => $flight['arrival_city'],
                'departure_date' => $flight['departure_date'],
                'travel_class' => 'ECONOMY'
            ]);
            $formatted = $duffelService->formatDuffelResults($rawResults);
            $unique = $duffelService->filterUniqueDuffelOffers($formatted);
            $offers = ['data' => $unique];
        } else {
            // --- Utilisation de Amadeus pour les autres compagnies ---
            $params = [
                'currencyCode' => 'MAD',
                'sources' => ['GDS'],
                'originDestinations' => [[
                    'id' => '1',
                    'originLocationCode' => strtoupper($flight['departure_city']),
                    'destinationLocationCode' => strtoupper($flight['arrival_city']),
                    'departureDateTimeRange' => [
                        'date' => $flight['departure_date'],
                    ],
                ]],
                'travelers' => [['id' => '1', 'travelerType' => 'ADULT']],
                'searchCriteria' => [
                    'flightFilters' => ['maxConnections' => 0],
                ],
            ];
            $offers = $this->amadeusService->searchFlightOffers($params, true);
        }

        if (!isset($offers['data']) || empty($offers['data'])) {
            throw new \Exception("Aucun vol trouvé pour ce segment.");
        }

        $filtered = [];

        foreach ($offers['data'] as $offerData) {
            foreach ($offerData['itineraries'] as $itinerary) {
                foreach ($itinerary['segments'] as $segment) {
                    $segmentCode = strtoupper($segment['carrierCode'] . $segment['number']);
                    $from = strtoupper($segment['departure']['iataCode'] ?? '');
                    $to = strtoupper($segment['arrival']['iataCode'] ?? '');

                    if (
                        $segmentCode === $flightNumberFull &&
                        $from === strtoupper($flight['departure_city']) &&
                        $to === strtoupper($flight['arrival_city'])
                    ) {
                        $filtered[] = [
                            'type' => $offerData['type'],
                            'id' => $offerData['id'],
                            'price' => $offerData['price'],
                            'validatingAirlineCodes' => $offerData['validatingAirlineCodes'],
                            'travelerPricings' => $offerData['travelerPricings'],
                            'itineraries' => [[
                                'segments' => [$segment],
                                'duration' => $itinerary['duration'] ?? ''
                            ]]
                        ];
                    }
                }
            }
        }

        if (empty($filtered)) {
            throw new \Exception("Aucun segment ne correspond au vol demandé.");
        }

        return [
            'status' => 'success',
            'data' => $filtered
        ];
    } catch (\Exception $e) {
        Log::error('❌ Erreur lors de la recherche Multi Flight Multiple', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return [
            'status' => 'error',
            'message' => $e->getMessage()
        ];
    }
}


public function fetchAmadeusResults(Request $request)
{
    $userId = auth()->id();
    $results = Cache::get("amadeus_results_user_{$userId}");

    if (!$results) {
        return response()->json(['status' => 'pending']);
    }

    if ($results['status'] === 'success') {
        return response()->json([
            'status' => 'success',
            'results' => view('Flight::admin.offer.partials.amadeus_results_direct_single', [
                'amadeusResults' => $results
            ])->render()
        ]);
    }

    return response()->json($results);
}

public function updateDirectSingle(Request $request)
{
    $validated = $request->validate([
        'outbound' => 'required|array',
        'returnOptions' => 'required|array'
    ]);

    $flightSearch = \App\Models\FlightSearch::latest()->first();

    if (!$flightSearch) {
        return response()->json(['success' => false, 'message' => 'Aucun enregistrement trouvé']);
    }

    // Si results est déjà un tableau
    $results = $flightSearch->results;

    if (!isset($results['data'][0]['data'])) {
        return response()->json(['success' => false, 'message' => 'Format de données invalide']);
    }

    $lastIndex = count($results['data'][0]['data']) - 1;
    $flightOffer = &$results['data'][0]['data'][$lastIndex];

    // Modification de l’aller
    $flightOffer['itineraries'][0]['segments'][0]['departure']['iataCode'] = $validated['outbound']['departureIata'];
    $flightOffer['itineraries'][0]['segments'][0]['departure']['at'] = $validated['outbound']['departureDate'] . 'T' . $validated['outbound']['departureTime'] . ':00';
    $flightOffer['itineraries'][0]['segments'][0]['arrival']['iataCode'] = $validated['outbound']['arrivalIata'];
    $flightOffer['itineraries'][0]['segments'][0]['arrival']['at'] = $validated['outbound']['arrivalDate'] . 'T' . $validated['outbound']['arrivalTime'] . ':00';
    $flightOffer['itineraries'][0]['segments'][0]['carrierCode'] = $validated['outbound']['carrierCode'];
    $flightOffer['itineraries'][0]['segments'][0]['number'] = $validated['outbound']['flightNumber'];

    // Modification du retour
    if (!empty($validated['returnOptions']) && isset($flightOffer['itineraries'][1]['segments'][0])) {
        $ret = $validated['returnOptions'][0];
        $flightOffer['itineraries'][1]['segments'][0]['departure']['iataCode'] = $ret['departureIata'];
        $flightOffer['itineraries'][1]['segments'][0]['departure']['at'] = $ret['departureDate'] . 'T' . $ret['departureTime'] . ':00';
        $flightOffer['itineraries'][1]['segments'][0]['arrival']['iataCode'] = $ret['arrivalIata'];
        $flightOffer['itineraries'][1]['segments'][0]['arrival']['at'] = $ret['arrivalDate'] . 'T' . $ret['arrivalTime'] . ':00';
        $flightOffer['itineraries'][1]['segments'][0]['carrierCode'] = $ret['carrierCode'];
        $flightOffer['itineraries'][1]['segments'][0]['number'] = $ret['flightNumber'];
    }

    // Sauvegarde (Laravel s'occupe de convertir en JSON si c'est casté)
    $flightSearch->results = $results;
    $flightSearch->save();

    return response()->json(['success' => true]);
}

public function updateDirectMultiple(Request $request)
{
    $validated = $request->validate([
        'outbound' => 'required|array',
        'returnOptions' => 'required|array'
    ]);

    $flightSearch = \App\Models\FlightSearch::latest()->first();

    if (!$flightSearch) {
        return response()->json(['success' => false, 'message' => 'Aucun enregistrement trouvé']);
    }

    // Si results est déjà un tableau
    $results = $flightSearch->results;

    if (!isset($results['data'][0]['data'])) {
        return response()->json(['success' => false, 'message' => 'Format de données invalide']);
    }

    $lastIndex = count($results['data'][0]['data']) - 1;
    $flightOffer = &$results['data'][0]['data'][$lastIndex];

    // Modification de l’aller
    $flightOffer['itineraries'][0]['segments'][0]['departure']['iataCode'] = $validated['outbound']['departureIata'];
    $flightOffer['itineraries'][0]['segments'][0]['departure']['at'] = $validated['outbound']['departureDate'] . 'T' . $validated['outbound']['departureTime'] . ':00';
    $flightOffer['itineraries'][0]['segments'][0]['arrival']['iataCode'] = $validated['outbound']['arrivalIata'];
    $flightOffer['itineraries'][0]['segments'][0]['arrival']['at'] = $validated['outbound']['arrivalDate'] . 'T' . $validated['outbound']['arrivalTime'] . ':00';
    $flightOffer['itineraries'][0]['segments'][0]['carrierCode'] = $validated['outbound']['carrierCode'];
    $flightOffer['itineraries'][0]['segments'][0]['number'] = $validated['outbound']['flightNumber'];

    // Modification du retour
    if (!empty($validated['returnOptions']) && isset($flightOffer['itineraries'][1]['segments'][0])) {
        $ret = $validated['returnOptions'][0];
        $flightOffer['itineraries'][1]['segments'][0]['departure']['iataCode'] = $ret['departureIata'];
        $flightOffer['itineraries'][1]['segments'][0]['departure']['at'] = $ret['departureDate'] . 'T' . $ret['departureTime'] . ':00';
        $flightOffer['itineraries'][1]['segments'][0]['arrival']['iataCode'] = $ret['arrivalIata'];
        $flightOffer['itineraries'][1]['segments'][0]['arrival']['at'] = $ret['arrivalDate'] . 'T' . $ret['arrivalTime'] . ':00';
        $flightOffer['itineraries'][1]['segments'][0]['carrierCode'] = $ret['carrierCode'];
        $flightOffer['itineraries'][1]['segments'][0]['number'] = $ret['flightNumber'];
    }

    // Sauvegarde (Laravel s'occupe de convertir en JSON si c'est casté)
    $flightSearch->results = $results;
    $flightSearch->save();

    return response()->json(['success' => true]);
}


public function updateMultiple(Request $request)
{
    $validated = $request->validate([
        'outbound' => 'required|array',
        'returnOptions' => 'required|array'
    ]);

    $flightSearch = \App\Models\FlightSearch::latest()->first();

    if (!$flightSearch) {
        return response()->json(['success' => false, 'message' => 'Aucun enregistrement trouvé']);
    }

    // Si results est déjà un tableau
    $results = $flightSearch->results;

    if (!isset($results['data'][0]['data'])) {
        return response()->json(['success' => false, 'message' => 'Format de données invalide']);
    }

    $lastIndex = count($results['data'][0]['data']) - 1;
    $flightOffer = &$results['data'][0]['data'][$lastIndex];

    // Modification de l’aller
    $flightOffer['itineraries'][0]['segments'][0]['departure']['iataCode'] = $validated['outbound']['departureIata'];
    $flightOffer['itineraries'][0]['segments'][0]['departure']['at'] = $validated['outbound']['departureDate'] . 'T' . $validated['outbound']['departureTime'] . ':00';
    $flightOffer['itineraries'][0]['segments'][0]['arrival']['iataCode'] = $validated['outbound']['arrivalIata'];
    $flightOffer['itineraries'][0]['segments'][0]['arrival']['at'] = $validated['outbound']['arrivalDate'] . 'T' . $validated['outbound']['arrivalTime'] . ':00';
    $flightOffer['itineraries'][0]['segments'][0]['carrierCode'] = $validated['outbound']['carrierCode'];
    $flightOffer['itineraries'][0]['segments'][0]['number'] = $validated['outbound']['flightNumber'];

    // Modification du retour
    if (!empty($validated['returnOptions']) && isset($flightOffer['itineraries'][1]['segments'][0])) {
        $ret = $validated['returnOptions'][0];
        $flightOffer['itineraries'][1]['segments'][0]['departure']['iataCode'] = $ret['departureIata'];
        $flightOffer['itineraries'][1]['segments'][0]['departure']['at'] = $ret['departureDate'] . 'T' . $ret['departureTime'] . ':00';
        $flightOffer['itineraries'][1]['segments'][0]['arrival']['iataCode'] = $ret['arrivalIata'];
        $flightOffer['itineraries'][1]['segments'][0]['arrival']['at'] = $ret['arrivalDate'] . 'T' . $ret['arrivalTime'] . ':00';
        $flightOffer['itineraries'][1]['segments'][0]['carrierCode'] = $ret['carrierCode'];
        $flightOffer['itineraries'][1]['segments'][0]['number'] = $ret['flightNumber'];
    }

    // Sauvegarde (Laravel s'occupe de convertir en JSON si c'est casté)
    $flightSearch->results = $results;
    $flightSearch->save();

    return response()->json(['success' => true]);
}


}