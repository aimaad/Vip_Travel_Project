<?php

namespace App\Jobs;

use App\Models\FlightSearch;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Http\Controllers\Modules\Flight\Admin\OfferController;

class SearchMultiFlightSingleInfoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $searchId;

    public function __construct(int $searchId)
    {
        $this->searchId = $searchId;
    }

    public function handle()
    {
        $search = FlightSearch::find($this->searchId);

        if (!$search) {
            Log::error("FlightSearch non trouvé pour ID: {$this->searchId}");
            return;
        }

        try {
            Log::info("Traitement multi flight single ID: {$this->searchId}");

            $offers = collect($search->search_params['offers'])->map(function ($offer) {
                return (object)[
                    'departure_city' => strtoupper($offer['outbound']['departure_city']),
                    'arrival_city' => strtoupper($offer['outbound']['arrival_city']),
                    'departure_date' => \Carbon\Carbon::parse($offer['outbound']['departure_date']),
                    'flight_number' => strtoupper($offer['outbound']['flight_number']),
                    'return_departure_city' => strtoupper($offer['return']['departure_city'] ?? ''),
                    'return_arrival_city' => strtoupper($offer['return']['arrival_city'] ?? ''),
                    'return_date' => !empty($offer['return']['return_date']) ? \Carbon\Carbon::parse($offer['return']['return_date']) : null,
                    'return_flight_number' => strtoupper($offer['return']['flight_number'] ?? ''),
                    'places' => $offer['places'] ?? null,
                    'price_adult' => $offer['price_adult'] ?? null,
                    'price_child' => $offer['price_child'] ?? null,
                    'price_baby' => $offer['price_baby'] ?? null,
                ];
            });

            $results = [];

            foreach ($offers as $offer) {
                $result = app(OfferController::class)->searchMultiFlightSingleInfo($offer);
                if ($result['status'] !== 'success') {
                    throw new \Exception($result['message']);
                }
                $results[] = $result['data'];
            }

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
            Log::error("Erreur dans SearchMultiFlightSingleInfoJob : " . $e->getMessage());

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
