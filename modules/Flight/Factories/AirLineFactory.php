<?php
namespace Modules\Flight\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Flight\Models\Airline;

class AirlineFactory extends Factory
{
    protected $model = Airline::class;

    public function definition()
    {
        return [
            'name' => $this->faker->company . ' Airlines',
            'iata_code' => strtoupper($this->faker->unique()->lexify('??')),
            'icao_code' => strtoupper($this->faker->unique()->lexify('???')),
            'callsign' => strtoupper($this->faker->word),
            'country_code' => $this->faker->countryCode,
            'description' => $this->faker->paragraph,
            'status' => true,
        ];
    }
}