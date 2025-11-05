<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Offre;
use App\Notifications\OffreRefuseeNotification;


class OffreController extends Controller
{
    public function validation($offreId)
    {
        $offre = Offre::with(['hotel_scraping', 'creator','offerFlights.flightLegs'])->findOrFail($offreId);
        return view('admin.offres.validation', compact('offre'));
    }

    public function valider($offreId)
    {
        $offre = Offre::with(['hotel_scraping', 'creator', 'offerFlights.flightLegs'])->findOrFail($offreId);

        $offre->statut = 'valide';
        $offre->save();
    
        // Notifier le créateur de l'offre
        if ($offre->creator && method_exists($offre->creator, 'notify')) {
            $offre->creator->notify(new \App\Notifications\OffreValideeNotification($offre));
        }
    
        return redirect()->back()->with('success', 'Offre validée.');
    }
    public function refuser(Request $request, $offreId)
    {
        $offre = Offre::with(['hotel_scraping', 'creator', 'offerFlights.flightLegs'])->findOrFail($offreId);
    
        $request->validate([
            'refus_commentaire' => 'required|string|max:2000'
        ]);
    
        $offre->statut = 'refusee';
        $offre->refus_commentaire = $request->refus_commentaire;
        $offre->save();
    
        // Notifier le créateur (par notif Laravel, e-mail, etc.)
        if ($offre->creator && method_exists($offre->creator, 'notify')) {
            $offre->creator->notify(new OffreRefuseeNotification($offre));
        }
    
        return redirect()->back()->with('success', 'Offre refusée et commentaire transmis à l’utilisateur.');
    }
    public function index()
    {
        $offres = Offre::with(['hotel_scraping', 'creator','offerFlights.flightLegs'])
            ->orderByDesc('created_at') // ou ->orderBy('created_at', 'desc')
            ->paginate(10); // ajuste le 10 selon le nombre d’offres par page voulu
    
        return view('admin.offres.index', compact('offres'));
    }
}