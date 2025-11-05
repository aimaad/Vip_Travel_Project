<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\AmadeusFlightService;
use App\Models\FlightSearch;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Modules\Flight\Admin\OfferController;



class SearchMultiFlightMultipleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $searchId;

    public function __construct(int $searchId)
    {
        $this->searchId = $searchId;
    }

    public function handle(AmadeusFlightService $amadeusService)
    {
        $search = FlightSearch::find($this->searchId);

        if (!$search) {
            Log::error("FlightSearch non trouvé pour ID: {$this->searchId}");
            return;
        }
        $params = $search->search_params; // JSON -> tableau

        try {
            $offers = $params['offers'];
            $results = [];
    
            foreach ($offers as $offerIndex => $offer) {
                $outboundFlights = $offer['outbound'];
                $returnFlights = $offer['return']; 
    
                // Handle outbound flights
                foreach ($outboundFlights as $legIndex => $flight) {
                    $searchResult =  app(OfferController::class)->searchMultiFlightMultipleInfo($flight) ;
    
                    if ($searchResult['status'] !== 'success') {
                        throw new \Exception($searchResult['message']);
                    }
    
                    // Normalize outbound flight data
                    $normalizedFlights = array_map(function ($flightOffer) {
                        return  app(OfferController::class)->normalizeFlightOffer($flightOffer);
                    }, $searchResult['data']);
    
                    $results['offers'][$offerIndex]['outbound'][$legIndex] = $normalizedFlights;
                }

                // Handle return flights
                foreach ($returnFlights as $legIndex => $flight) {
                    $searchResult =  app(OfferController::class)->searchMultiFlightMultipleInfo($flight);
    
                    if ($searchResult['status'] !== 'success') {
                        throw new \Exception($searchResult['message']);
                    }
    
                    // Normalize return flight data
                    $normalizedFlights = array_map(function ($flightOffer) {
                        return  app(OfferController::class)->normalizeFlightOffer($flightOffer);
                    }, $searchResult['data']);
    
                    $results['offers'][$offerIndex]['return'][$legIndex] = $normalizedFlights;
                }
            }
            Log::info('Results sent to view:', $results);
    
            $search->update([
                'results' => $results,
                'status' => 'success',
                'places' => $params['places'] ?? null,
                'price_adult' => $params['price_adult'] ?? null,
                'price_child' => $params['price_child'] ?? null,
                'price_baby' => $params['price_baby'] ?? null,
                'error_message' => null,
            ]);
    
        } catch (\Exception $e) {
           
            Log::error("Erreur dans la recherche #{$this->searchId} : {$e->getMessage()}");

            
            $search->update([
                'status' => 'error',
                'places' => $params['places'] ?? null,
                'price_adult' => $params['price_adult'] ?? null,
                'price_child' => $params['price_child'] ?? null,
                'price_baby' => $params['price_baby'] ?? null,
                'error_message' => $e->getMessage(),
            ]);
        }
    }
}