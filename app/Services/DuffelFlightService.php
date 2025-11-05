<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DuffelFlightService
{
    protected $apiUrl = 'https://api.duffel.com/air/offer_requests';
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env('DUFFEL_API_KEY');
    }

    /**
     * Recherche des offres de vol via Duffel
     */
    public function searchFlightOffers(array $params)
    {
        $slices = [
            [
                "origin" => strtoupper($params['departure_city']),
                "destination" => strtoupper($params['arrival_city']),
                "departure_date" => $params['departure_date'],
            ]
        ];
    
        if (!empty($params['return_departure_city']) &&
            !empty($params['return_arrival_city']) &&
            !empty($params['return_date'])) {
    
            $slices[] = [
                "origin" => strtoupper($params['return_departure_city']),
                "destination" => strtoupper($params['return_arrival_city']),
                "departure_date" => $params['return_date'],
            ];
        }
    
        $payload = [
            'data' => [
                "slices" => $slices,
                "passengers" => [
                    ["type" => "adult"]
                ],
                "cabin_class" => strtolower($params['travel_class'] ?? 'economy'),
            ]
        ];
    
        Log::info('Duffel API Request Payload:', $payload);
    
        try {
            $response = Http::withToken($this->apiKey)
                ->withHeaders([
                    'Duffel-Version' => 'v2',
                    'Content-Type' => 'application/json',
                ])
                ->post($this->apiUrl, $payload);
    
            if ($response->failed()) {
                Log::error('Duffel API Error Response:', ['body' => $response->body()]);
                throw new \Exception("Duffel API Error: " . $response->body());
            }
    
            $responseData = $response->json();
            Log::info('Duffel API Response:', $responseData);
    
            return $responseData;
    
        } catch (\Exception $e) {
            Log::error('Duffel API Exception:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
    

    /**
     * Formatage des résultats Duffel pour correspondre au format attendu
     */
    public function formatDuffelResults(array $duffelOffers): array
    {
        $formatted = [];
    
        foreach ($duffelOffers['data']['offers'] ?? [] as $offer) {
            $itineraries = [];
    
            foreach ($offer['slices'] as $slice) {
                $segments = [];
    
                if (!isset($slice['segments']) || !is_array($slice['segments'])) {
                    continue;
                }
    
                foreach ($slice['segments'] as $segment) {
                    $segments[] = [
                        'departure' => [
                            'iataCode' => $segment['origin']['iata_code'] ?? 'N/A',
                            'at' => $segment['departing_at'] ?? null,
                        ],
                        'arrival' => [
                            'iataCode' => $segment['destination']['iata_code'] ?? 'N/A',
                            'at' => $segment['arriving_at'] ?? null,
                        ],
                        'carrierCode' => $segment['operating_carrier']['iata_code'] ?? 'N/A',
                        'number' => $segment['marketing_carrier_flight_number']
                            ?? $segment['operating_carrier_flight_number']
                            ?? null,
                        'aircraft' => [
                            'code' => $segment['aircraft']['name'] ?? 'N/A',
                        ],
                        'duration' => $segment['duration'] ?? 'N/A',
                    ];
                }
    
                $itineraries[] = [
                    'segments' => $segments,
                    'duration' => $slice['duration'] ?? null,
                ];
            }
    
            // Simulation de travelerPricings
            $travelerPricings = [];
    
            $priceTotal = $offer['total_amount'] ?? null;
    
            if ($priceTotal) {
                $travelerPricings[] = [
                    'travelerType' => 'ADULT',
                    'price' => [
                        'total' => $priceTotal,
                        'currency' => $offer['total_currency'] ?? 'EUR',
                    ]
                ];
            }
    
            $formatted[] = [
                'type' => 'flight-offer',
                'id' => $offer['id'] ?? null,
                'itineraries' => $itineraries,
                'price' => [
                    'total' => $priceTotal ?? 'N/A',
                    'currency' => $offer['total_currency'] ?? 'N/A',
                ],
                'validatingAirlineCodes' => [
                    $offer['owner']['iata_code'] ?? 'N/A'
                ],
                'travelerPricings' => $travelerPricings,
            ];
        }
    
        return $formatted;
    }
    
    function filterUniqueDuffelOffers(array $offers): array
{
    $seen = [];
    $unique = [];

    foreach ($offers as $offer) {
        // Construire une clé unique basée sur les segments aller-retour
        $key = '';
        foreach ($offer['itineraries'] as $itinerary) {
            foreach ($itinerary['segments'] as $segment) {
                $key .= $segment['departure']['iataCode'] 
                      . $segment['departure']['at'] 
                      . $segment['arrival']['iataCode'] 
                      . $segment['arrival']['at'] 
                      . $segment['carrierCode'] 
                      . $segment['number'];
            }
        }

        if (!isset($seen[$key])) {
            $seen[$key] = true;
            $unique[] = $offer;
        }
    }

    return $unique;
}

    
}
