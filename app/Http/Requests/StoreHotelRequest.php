<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHotelRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'total_rooms' => 'required|integer|min:1',
            'room_types' => 'required|array|min:1',
            'room_types.*.type' => 'required|in:single,double,triple',
            'room_types.*.adults' => 'required|integer|min:0',
            'room_types.*.children' => 'required|integer|min:0',
            'room_types.*.kids' => 'required|integer|min:0',
            'room_types.*.babies' => 'required|integer|min:0',
            'room_types.*.price' => 'required|numeric|min:0',
            'room_types.*.available_rooms' => 'required|integer|min:0',
            'room_types.*.pension' => 'required|in:RO,PDJ,DP,PC',
           
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $totalAvailable = 0;
            $totalRooms = $this->input('total_rooms');
            $roomTypes = $this->input('room_types');

            if (!is_array($roomTypes)) {
                return;
            }
            foreach ($this->input('room_types') as $index => $room) {
                $adults = $room['adults'];
                $children = $room['children'];
                $kids = $room['kids'];
                $babies = $room['babies'];
                $totalAvailable += $room['available_rooms'];

                switch ($room['type']) {
                    case 'single':
                        if ($adults > 1 || ($children + $kids + $babies) > 3) {
                            $validator->errors()->add("room_types.$index", "Occupation max d'une chambre single est 1 adulte + 3 mineurs.");
                        }
                        break;
                    case 'double':
                        if ($adults > 2 || ($children + $kids + $babies) > 2) {
                            $validator->errors()->add("room_types.$index", "Occupation max d'une double est 2 adultes + 2 mineurs.");
                        }
                        break;
                    case 'triple':
                        if ($adults > 3 || ($children + $kids) > 0 || $babies > 1) {
                            $validator->errors()->add("room_types.$index", "Une triple accepte 3 adultes + 1 bébé uniquement.");
                        }
                        break;
                }
            }

            if ($totalAvailable > $totalRooms) {
                $validator->errors()->add("room_types", "La somme des chambres en vente dépasse le nombre total de chambres.");
            }
        });
    }
    public function messages()
{
    return [
        'room_types.required' => 'Vous devez saisir au moins un type de chambre.',
        'room_types.array'    => 'Vous devez saisir au moins un type de chambre.',
        'room_types.min'      => 'Vous devez saisir au moins un type de chambre.',
        'hotel_url.regex' => "L'URL doit être une adresse valide de Booking.com.",

    ];
}
}

