<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Http\Controllers\Modules\Flight\Admin\OfferController;
use App\Models\FlightSearch;

class ProcessDirectMultipleOffer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $offer;
    protected $places;
protected $price_adult;
protected $price_child;
protected $price_baby;
protected $user_id; 
protected $flight_type;

    public function __construct( $offer, $places, $price_adult, $price_child, $price_baby, $user_id,$flight_type)
    {
        
        $this->offer = $offer;
        $this->places = $places;
        $this->price_adult = $price_adult;
        $this->price_child = $price_child;
        $this->price_baby = $price_baby;
        $this->user_id = $user_id;
        $this->flight_type = $flight_type;
    }
    
   public function handle()
{
    $search = new FlightSearch();

    try {
        $controller = app(OfferController::class); 

        $search->user_id = $this->user_id;
        
        $search->search_params = json_encode($this->offer);
        $search->status = 'pending';
        $search->places = $this->places;
        $search->price_adult = $this->price_adult;
        $search->price_child = $this->price_child;
        $search->price_baby = $this->price_baby;
        $search->type = 'direct_multiple';
        $search->save();

        $amadeusResults = $controller->searchFlightInfoForMultipleFlights($this->offer);

        if ($amadeusResults['status'] !== 'success') {
            throw new \Exception($amadeusResults['message']);
        }

        $formattedResults = [
            'flights' => collect($amadeusResults['data']['flights'])->map(function ($group) {
                return collect($group)->values()->map(function ($offer) {
                    return [
                        'id' => $offer['id'],
                        'price' => $offer['price']['total'],
                        'currency' => $offer['price']['currency'],
                        'itineraries' => collect($offer['itineraries'])->map(function ($itinerary) {
                            return [
                                'segments' => collect($itinerary['segments'])->map(function ($segment) {
                                    return [
                                        'carrierCode' => $segment['carrierCode'] ?? null,
                                        'number' => $segment['number'] ?? null,
                                        'departure' => $segment['departure'] ?? null,
                                        'arrival' => $segment['arrival'] ?? null,
                                    ];
                                }),
                            ];
                        }),
                    ];
                });
            }),
            'return_flights' => collect($amadeusResults['data']['return_flights'])->map(function ($group) {
                return collect($group)->values()->map(function ($offer) {
                    return [
                        'id' => $offer['id'],
                        'price' => $offer['price']['total'],
                        'currency' => $offer['price']['currency'],
                        'itineraries' => collect($offer['itineraries'])->map(function ($itinerary) {
                            return [
                                'segments' => collect($itinerary['segments'])->map(function ($segment) {
                                    return [
                                        'carrierCode' => $segment['carrierCode'] ?? null,
                                        'number' => $segment['number'] ?? null,
                                        'departure' => $segment['departure'] ?? null,
                                        'arrival' => $segment['arrival'] ?? null,
                                    ];
                                }),
                            ];
                        }),
                    ];
                });
            }),
        ];

        $html = view('Flight::admin.offer.partials.amadeus_results_direct_multiple', [
            'amadeusResults' => $formattedResults,
        ])->render();

        $search->results = json_encode($formattedResults);
        $search->status = 'success';
        $search->save();

       

    } catch (\Exception $e) {
        $search->status = 'error';
        $search->error_message = $e->getMessage();
        $search->save();

        
    }
}

}
