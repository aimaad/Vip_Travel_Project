<?php

namespace App\Services;

use Amadeus\Amadeus;
use Amadeus\Exceptions\ResponseException;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class AmadeusFlightService
{
    protected $amadeus;
    protected $client;
    protected $accessToken;
    protected $baseUri;

    public function __construct()
    {
        $this->baseUri = 'https://api.amadeus.com/' ;

        $this->client = new Client([
            'base_uri' => $this->baseUri,
            'timeout'  => 30,
        ]);
        
        $this->amadeus = Amadeus::builder(
            config('services.amadeus.key'),
            config('services.amadeus.secret')
        )->build();
    }

    /**
     * Obtient un token d'accès Amadeus
     */
    protected function getAccessToken()
    {
        try {
            $response = $this->client->post('v1/security/oauth2/token', [
                'form_params' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => config('services.amadeus.key'),
                    'client_secret' => config('services.amadeus.secret')
                ],
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    // Supprimez l'Authorization header s'il était présent
                ]
            ]);
    
            $data = json_decode($response->getBody(), true);
            
            if (!isset($data['access_token'])) {
                throw new \Exception("Failed to get access token: " . ($data['error_description'] ?? 'Unknown error'));
            }
    
            return $data['access_token'];
    
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $responseBody = $e->hasResponse() ? (string) $e->getResponse()->getBody() : null;
        
            Log::error('Amadeus Token Error', [
                'message' => $e->getMessage(),
                'response_body' => $responseBody,
                'trace' => $e->getTraceAsString()
            ]);
        
            throw new \Exception("Could not get Amadeus access token: " . ($responseBody ?? $e->getMessage()));
        }
        
    }

    /**
     * Recherche des offres de vol
     */
    public function searchFlightOffers(array $params, bool $usePost = false)
    {
        Log::info('📦 Appel Amadeus avec les paramètres suivants :', $params);
    
        try {
            if ($usePost) {
                return $this->makePostRequest($params);
            }
    
            return $this->makeGetRequest($params);
    
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $responseBody = $e->hasResponse() ? (string) $e->getResponse()->getBody() : null;
    
            Log::error('Amadeus API RequestException', [
                'message' => $e->getMessage(),
                'response_body' => $responseBody,
                'trace' => $e->getTraceAsString()
            ]);
    
            throw new \Exception('Service Error: ' . ($responseBody ?? $e->getMessage()));
        } catch (\Exception $e) {
            Log::error('Amadeus Service Error (Exception)', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception('Service Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Effectue une requête POST avec body
     */
    protected function makePostRequest(array $params)
    {
        $token = $this->getAccessToken();
        
        $response = $this->client->post('v2/shopping/flight-offers', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json', // Modifié depuis application/vnd.amadeus+json
            ],
            'json' => $params // Gardez la structure telle quelle
        ]);
        
        return $this->processResponse($response);
    }
    /**
     * Effectue une requête GET avec paramètres query
     */
    protected function makeGetRequest(array $params)
    {
        $response = $this->amadeus->getShopping()->getFlightOffers()->get($params);
        
        if (is_array($response)) {
            return $response;
        }
        
        return $response->getResult();
    }

    /**
     * Traite la réponse de l'API
     */
    protected function processResponse($response)
    {
        $data = json_decode($response->getBody(), true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Invalid JSON response");
        }
        
        if ($response->getStatusCode() !== 200) {
            throw new \Exception($data['errors'][0]['detail'] ?? 'Unknown API error');
        }
        
        return $data;
    }

    /**
     * Journalise les erreurs Amadeus
     */
   protected function logAmadeusError(ResponseException $e)
{
    $response = method_exists($e, 'getResponse') ? $e->getResponse() : null;

    $responseBody = $response ? (string) $response->getBody() : null;

    Log::error('Amadeus API Error', [
        'code' => $e->getCode(),
        'message' => $e->getMessage(),
        'response_body' => $responseBody,
        'trace' => $e->getTraceAsString()
    ]);

    // Pour test/debug immédiat (optionnel)
    // dd(json_decode($responseBody, true));
}

}