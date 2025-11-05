<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;
use App\Models\HotelScraping;


class BookingScraperService
{
    protected string $apiKey;

    public function __construct()
    {
        $this->apiKey = env('SCRAPER_API_KEY'); // ajouter cette variable dans .env
    }

    // Méthode pour scraper directement avec l'URL de l'hôtel

    public function scrapeByHotelNameAndCity(string $hotelName, string $city): array
    {
        $url = $this->findHotelUrlByName($hotelName, $city);
    
        if (!$url) {
            return ['error' => 'Aucun hôtel correspondant trouvé.'];
        }
    
        return $this->scrapeHotelPage($url, $hotelName);
    }
    

    protected function scrapeHotelPage(string $url,string $hotelName): array
    {


        // Vérifier si ce nom d'hôtel a déjà été scrapé
    $existing = HotelScraping::where('hotel_name', $hotelName)->first();

    if ($existing) {
        return [
            'id'=> $existing->id,
            'images' => $existing->images, 
            'address' => $existing->address,
            'rating' => $existing->rating,
        ];
    }

        $html = $this->fetchViaScraperAPI($url);
        $crawler = new Crawler($html);

        $data = [];

        $data['images'] = $crawler->filter('img')->each(function ($node) {
            $src = $node->attr('src');
        
            // Conserver uniquement les images d'hôtels/chambres
            if (
                $src &&
                str_contains($src, 'xdata/images/hotel/')
            ) {
                return $src;
            }
        
            return null; // ignorer les autres images
        });
        
        // Supprimer les valeurs nulles
        $data['images'] = array_values(array_filter($data['images']));
        

   // Récupérer la note de l'hôtel
   try {
    $data['rating'] = trim($crawler->filter('.f63b14ab7a')->text());
} catch (\Exception $e) {
    $data['rating'] = 'Note non trouvée';
}

// Récupérer l'adresse de l'hôtel
try {
    $addressNode = $crawler->filter('.b99b6ef58f.cb4b7a25d9')->first();
    $fullText = $addressNode->text();

    // Supprimer le texte des enfants pour ne garder que l'adresse
    $childrenText = '';
    foreach ($addressNode->children() as $child) {
        $childrenText .= $child->textContent;
    }

    $data['address'] = trim(str_replace($childrenText, '', $fullText));
} catch (\Exception $e) {
    $data['address'] = 'Adresse non trouvée';
}


// Après avoir rempli $data['images'], $data['address'], $data['rating']
$warningMessages = [];

if (empty($data['images'])) {
    $warningMessages[] = "Aucune image n’a été trouvée.";
}

if ($data['address'] === 'Adresse non trouvée') {
    $warningMessages[] = "Adresse non trouvée.";
}

if ($data['rating'] === 'Note non trouvée') {
    $warningMessages[] = "Note de l’hôtel non trouvée.";
}

HotelScraping::create([
    'hotel_name' => $hotelName,
    'images' => $data['images'], 
    'address' => $data['address'],
    'rating' => $data['rating'],
]);


// Ajouter les warnings si nécessaires
if (!empty($warningMessages)) {
    $data['warning'] = implode(' ', $warningMessages);
}

return $data;

    }


    



    protected function fetchViaScraperAPI(string $targetUrl): string
    {
        try {
            \Log::debug("URL envoyée à ScraperAPI: {$targetUrl}");
         
            $response = Http::timeout(60)->get("http://api.scraperapi.com", [
                'api_key' => $this->apiKey,
                'url' => $targetUrl,
                'render' => 'true',
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/117.0 Safari/537.36',
                    'Accept-Language' => 'fr-FR,fr;q=0.9'
                ]
            ]);

            if (!$response->successful()) {
                throw new \Exception("Erreur ScraperAPI: " . $response->status() . " - " . $response->body());
            }

            return $response->body();

        } catch (\Illuminate\Http\Client\RequestException $e) {
            throw new \Exception("Erreur réseau lors de l'appel à ScraperAPI : " . $e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception("Erreur générale : " . $e->getMessage());
        }
    }


    protected function findHotelUrlByName(string $hotelName, string $city): ?string
    {
        $query = urlencode($hotelName . ' ' . $city);
        $searchUrl = "https://www.booking.com/searchresults.html?ss={$query}";
    
        $html = $this->fetchViaScraperAPI($searchUrl);
    
        try {
            $crawler = new Crawler($html);
            $link = $crawler->filter('a[data-testid="title-link"]')->first()->attr('href');
    
            if ($link) {
                $link = html_entity_decode(trim($link));
    
                // 🛠️ Correction ici : éviter d'ajouter deux fois le domaine
                if (!str_starts_with($link, 'http')) {
                    $link = 'https://www.booking.com' . $link;
                }
    
                \Log::debug("URL Booking trouvée : $link");
    
                if (filter_var($link, FILTER_VALIDATE_URL)) {
                    return $link;
                } else {
                    \Log::warning("URL générée invalide : $link");
                }
            }
        } catch (\Exception $e) {
            \Log::warning("Échec parsing DOM dans findHotelUrlByName: " . $e->getMessage());
        }
    
        \Log::warning("Aucun hôtel trouvé pour {$hotelName}, {$city}");
        return null;
    }
    
    
    

}
