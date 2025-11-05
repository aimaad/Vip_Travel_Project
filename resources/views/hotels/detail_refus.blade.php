@extends('admin.layouts.app')
@section('content')
<div class="container">
    <div class="alert alert-danger">
        <h4>Offre refusée par l'administrateur</h4>
        <p>
            Votre offre pour l'hôtel <strong>{{ $hotelScraping->hotel_name ?? '-' }}</strong> a été refusée.<br>
            <strong>Motif du refus :</strong>
            <br>
            <span class="text-warning">{{ $offre->refus_commentaire ?? 'Aucun motif précisé.' }}</span>
        </p>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            Détails de l'offre
        </div>
        <div class="card-body">
            <p><strong>Nom de l'hôtel :</strong> {{ $hotelScraping->hotel_name ?? '-' }}</p>
            <p><strong>Adresse :</strong> {{ $hotelScraping->address ?? '-' }}</p>
            <p><strong>Nombre total de chambres :</strong> {{ $offre->total_rooms ?? '-' }}</p>
            @php
                $types = is_array($offre->room_types) 
                    ? $offre->room_types 
                    : (json_decode($offre->room_types, true) ?? []);
            @endphp
            
            <p><strong>Types de chambres :</strong> 
                {{ implode(', ', array_map(function($room) {
                    return is_array($room) && isset($room['type']) ? $room['type'] : $room;
                }, $types)) ?: '-' }}
            </p>
            <p><strong>Statut :</strong> {{ ucfirst($offre->statut) }}</p>
            <p><strong>Date de création :</strong> {{ $offre->created_at ? \Carbon\Carbon::parse($offre->created_at)->format('d/m/Y H:i') : '-' }}</p>
        </div>
    </div>
    <div class="card mb-4">
        <div class="card-header">Détails des Vols</div>
        <div class="card-body">
            @if(count($offerFlights))
                @foreach($offerFlights as $flight)
                    <div class="mb-4 p-3 border rounded">
                        <h6 class="text-primary fw-bold">
                            Type : {{ ucfirst($flight->flight_type) }}
                        </h6>
    
                        <div class="row mb-3">
                            <div class="col"><strong>Places :</strong> {{ $flight->places }}</div>
                            <div class="col"><strong>Adulte :</strong> {{ $flight->price_adult }} €</div>
                            <div class="col"><strong>Enfant :</strong> {{ $flight->price_child }} €</div>
                            <div class="col"><strong>Bébé :</strong> {{ $flight->price_baby }} €</div>
                        </div>
    
                        @foreach($flight->flightLegs as $leg)
                            <div class="p-3 mb-2 border bg-light rounded">
                                <h6 class="text-secondary">Trajet : {{ ucfirst($leg->direction) }}</h6>
                                <ul class="mb-0 list-unstyled">
                                    <li><strong>Numéro de vol :</strong> {{ $leg->flight_number }}</li>
                                    <li><strong>Départ :</strong> {{ $leg->departure_city }} le {{ $leg->departure_date }} à {{ $leg->departure_time }}</li>
                                    <li><strong>Arrivée :</strong> {{ $leg->arrival_city }} le {{ $leg->arrival_date }} à {{ $leg->arrival_time }}</li>
                                    <li><strong>Compagnie :</strong>
                                        @if($leg->airline_logo)
                                            <img src="{{ $leg->airline_logo }}" alt="logo" style="height: 20px;">
                                        @else
                                            <em>Non fournie</em>
                                        @endif
                                    </li>
                                </ul>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            @else
                <div class="alert alert-warning">Aucun vol associé à cette offre.</div>
            @endif
        </div>
    </div>
    
 <!-- <a href="{{ route('hotels.confirm', ['offre' => $offre->id]) }}" class="btn btn-primary">
        Modifier et soumettre à nouveau
    </a>
    <a href="{{ route('mes.offres') }}" class="btn btn-secondary">
        Retour à mes offres
    </a>
-->
</div>
@endsection