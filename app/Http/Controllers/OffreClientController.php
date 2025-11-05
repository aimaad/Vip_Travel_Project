<?php

namespace App\Http\Controllers;

use App\Models\Offre;

class OffreClientController extends Controller
{
    public function index()
    {
        $offres = Offre::where('statut', 'valide')->with('hotel_Scraping','offerFlights.flightLegs')->latest()->paginate(9);
        return view('offres.index', compact('offres'));
    }

    public function show($id)
    {
        $offre = Offre::with('hotel_Scraping','offerFlights.flightLegs')->findOrFail($id);
        if ($offre->statut !== 'valide') abort(403);
        $services = $offre->services;
        return view('offres.show', compact('offre', 'services'));
    }
}