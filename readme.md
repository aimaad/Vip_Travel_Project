# VipTravel Application

## Installation

1. Clone the project:
   git clone https://github.com/aimaad/Vip_Travel_Project.git
   cd VIP_TRAVEL_PROJECT

2. Install PHP dependencies:
   composer install

3. Install Node dependencies:
   npm install

4. Generate the key:
   php artisan key:generate


## .env File Configuration

Open the .env file and configure:

- Database connection:
  DB_DATABASE=xxxx
  DB_USERNAME=xxxx
  DB_PASSWORD=xxxx

- Amadeus keys:
  AMADEUS_KEY=xxxx
  AMADEUS_SECRET=xxxx

- Duffel key:
  DUFFEL_API_KEY=xxxx

- Scraper API key:
  SCRAPER_API_KEY=xxxx


## Running the Project

Open 3 terminals and run:

1) Start the Laravel server:
   php artisan serve --port=8081

2) Start the frontend build:
   npm run dev

3) Start the queue system (asynchronous jobs):
   php artisan queue:work

The application will be accessible at:
http://127.0.0.1:8081/


## Code Information

- Main offers controller: OfferController
- Main hotels controller: HotelController


## Asynchronous Mode

The project uses a Jobs system to execute certain tasks in the background.
All jobs are located in:
app/Jobs/
