<?php

namespace App\Jobs;

use App\Services\BookingScraperService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ScrapeHotelData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $hotelName;
    protected $city;

    public function __construct($hotelName, $city)
    {
        $this->hotelName = $hotelName;
        $this->city = $city;
    }

    public function connection()
    {
        return 'database';  
    }

    public function handle(BookingScraperService $scraper)
    {
        \Log::info('Scraping automatique lancé pour '.$this->hotelName.' à '.$this->city);
        $scraper->scrapeByHotelNameAndCity($this->hotelName, $this->city);
        \Log::info('Scraping terminé pour '.$this->hotelName);
    }
}
