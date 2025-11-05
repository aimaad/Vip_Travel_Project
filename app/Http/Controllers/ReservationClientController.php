<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Offre;
use Illuminate\Http\Request;

class ReservationClientController extends Controller
{
    public function store(Request $request, $offre_id)
    {
        $offre = Offre::findOrFail($offre_id);

        $request->validate([
            'nom_client' => 'required',
            'email' => 'required|email',
            'nombre_personnes' => 'required|integer|min:1',
            'date_arrivee' => 'required|date|after_or_equal:today',
            'date_depart' => 'required|date|after:date_arrivee',
        ]);

        Reservation::create([
            'offre_id' => $offre->id,
            'user_id' => auth()->id(),
            'nom_client' => $request->nom_client,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'nombre_personnes' => $request->nombre_personnes,
            'date_arrivee' => $request->date_arrivee,
            'date_depart' => $request->date_depart,
            'commentaire' => $request->commentaire,
        ]);

        return redirect()->route('offres.show', $offre->id)
            ->with('success', 'Votre demande de réservation a bien été enregistrée !');
    }
}