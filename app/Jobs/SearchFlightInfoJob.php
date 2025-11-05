<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\AmadeusFlightService;
use App\Models\FlightSearch;

class SearchFlightInfoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $searchId;

    public function __construct(int $searchId)
    {
        $this->searchId = $searchId;
    }

    public function handle(AmadeusFlightService $amadeusService)
    {  log::info('dkhle ljob ');
        $search = FlightSearch::find($this->searchId);

        if (!$search) {
            Log::error("FlightSearch non trouvé pour ID: {$this->searchId}");
            return;
        }

        try {
            Log::info("Traitement recherche ID: {$this->searchId}");

            $params = $search->search_params;

            $offer = new \stdClass();
            $offer->flights = collect([
                (object)[
                    'departure_city' => strtoupper($params['departure_city']),
                    'arrival_city' => strtoupper($params['arrival_city']),
                    'departure_date' => \Carbon\Carbon::parse($params['departure_date']),
                    'return_date' => !empty($params['return_date']) ? \Carbon\Carbon::parse($params['return_date']) : null,
                ]
            ]);

            $results = app('App\Http\Controllers\Modules\Flight\Admin\OfferController')->searchFlightInfo($offer, $params);

            $search->update([
                'results' => $results,
                'status' => $results['status'],
                'error_message' => $results['status'] === 'error' ? $results['message'] : null,
                'places' => $params['places'] ?? null,
    'price_adult' => $params['price_adult'] ?? null,
    'price_child' => $params['price_child'] ?? null,
    'price_baby' => $params['price_baby'] ?? null,
              
            ]);
        } catch (\Exception $e) {
            Log::error("Erreur dans SearchFlightInfoJob : " . $e->getMessage());

            $search->update([
                'status' => 'error',
                'error_message' => $e->getMessage(),
                'places' => $params['places'] ?? null,
    'price_adult' => $params['price_adult'] ?? null,
    'price_child' => $params['price_child'] ?? null,
    'price_baby' => $params['price_baby'] ?? null,
            ]);
        }
    }
}