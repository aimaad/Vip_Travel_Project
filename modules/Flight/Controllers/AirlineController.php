<?php

namespace  Modules\Flight\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Airline;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Validation\Rule;

class AirlineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $airlines = Airline::all();
        return view('admin.airlines.index', compact('airlines'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.airlines.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->merge([
            'iata_code' => strtoupper($request->iata_code),
            'domain' => strtolower($request->domain)
        ]);
    
        $validator = Validator::make($request->all(), [
            'iata_code' => [
                'required',
                'string',
                'size:2',
                Rule::unique('airlines')->where(function ($query) use ($request) {
                    return $query->where('iata_code', strtoupper($request->iata_code))
                                 ->orWhere('domain', strtolower($request->domain));
                })
            ],
            'name' => 'required|string|max:255',
            'domain' => 'required|string|regex:/^[a-zA-Z0-9-]+\.[a-zA-Z]{2,}$/'
        ]);
    
        // Messages d'erreur personnalisés
        $validator->after(function ($validator) use ($request) {
            $existingByCode = Airline::where('iata_code', strtoupper($request->iata_code))->exists();
            $existingByDomain = Airline::where('domain', strtolower($request->domain))->exists();
    
            if ($existingByCode) {
                $validator->errors()->add('iata_code', 'Ce code IATA est déjà utilisé');
            }
    
            if ($existingByDomain) {
                $validator->errors()->add('domain', 'Ce domaine est déjà enregistré');
            }
        });
    
        if ($validator->fails()) {
            return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
        }
    
        Airline::create([
            'iata_code' => $request->iata_code,
            'name' => $request->name,
            'domain' => $request->domain,
        ]);
    
        return redirect()->route('admin.airlines.index')
                       ->with('success', 'Compagnie aérienne ajoutée avec succès');
    }
    /**
     * Display the specified resource.
     */
    public function show(Airline $airline)
    {
        return view('admin.airlines.show', compact('airline'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Airline $airline)
    {
        return view('admin.airlines.edit', compact('airline'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Airline $airline)
    {
        $validated = $request->validate([
            'iata_code' => 'required|string|size:2|unique:airlines,iata_code,'.$airline->id,
            'name' => 'required|string|max:255',
            'logo_url' => 'required|url',
        ]);

        $airline->update($validated);

        return redirect()->route('admin.airlines.index')
            ->with('success', 'Compagnie aérienne mise à jour avec succès');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Airline $airline)
    {
        $airline->delete();

        return redirect()->route('admin.airlines.index')
            ->with('success', 'Compagnie aérienne supprimée avec succès');
    }
}