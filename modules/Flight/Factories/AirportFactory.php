<?php

namespace Modules\Flight\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;
use Modules\Flight\Models\Airline;
use Modules\Flight\Models\Airport;
use Modules\Location\Models\Location;


class AirportFactory extends Factory
{
    protected $model = Airport::class;

    public function definition()
    {
        return [
            'name' => $this->faker->city . ' Airport',
            'code' => strtoupper($this->faker->unique()->lexify('???')),
            'description' => $this->faker->paragraph,
            'address' => $this->faker->address,
            'country_code' => $this->faker->countryCode,
            'city_code' => $this->faker->cityCode,
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
            'status' => true,
        ];
    }
}