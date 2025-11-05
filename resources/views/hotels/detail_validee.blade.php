@extends('admin.layouts.app')

@section('content')
<div class="container">
    <div class="alert alert-success">
        <h4>Votre offre a été validée !</h4>
        <p>
            L'offre pour l'hôtel <strong>{{ $hotelScraping->hotel_name ?? '-' }}</strong> a été <strong>validée</strong> par l'administrateur.
        </p>
    </div>

    <div class="card mb-4">
        <div class="card-header">Détails de l'offre validée</div>
        <div class="card-body">
            <p><strong>Nom de l'hôtel :</strong> {{ $hotelScraping->hotel_name ?? '-' }}</p>
            <p><strong>Adresse :</strong> {{ $hotelScraping->address ?? '-' }}</p>
            <p><strong>Nombre total de chambres :</strong> {{ $offre->total_rooms ?? '-' }}</p>
            <p><strong>Statut :</strong> {{ ucfirst($offre->statut) }}</p>
            <p><strong>Date de validation :</strong> {{ $offre->updated_at ? \Carbon\Carbon::parse($offre->updated_at)->format('d/m/Y H:i') : '-' }}</p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Types de chambres</div>
        <div class="card-body">
            @if(!empty($types))
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Adultes</th>
                            <th>Enfants</th>
                            <th>Kids</th>
                            <th>Bébés</th>
                            <th>Chambres dispo</th>
                            <th>Pension</th>
                            <th>Prix (€)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($types as $room)
                            <tr>
                                <td>{{ $room['type'] ?? '-' }}</td>
                                <td>{{ $room['adults'] ?? '-' }}</td>
                                <td>{{ $room['children'] ?? '-' }}</td>
                                <td>{{ $room['kids'] ?? '-' }}</td>
                                <td>{{ $room['babies'] ?? '-' }}</td>
                                <td>{{ $room['available_rooms'] ?? '-' }}</td>
                                <td>{{ $room['pension'] ?? '-' }}</td>
                                <td>{{ $room['price'] ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="alert alert-warning">Aucun type de chambre renseigné.</div>
            @endif
        </div>
    </div>
    <div class="card mb-4">
        <div class="card-header">Données scrappées Booking</div>
        <div class="card-body">
            <p><strong>Adresse :</strong> {{ $scraped['address'] ?? '-' }}</p>
            <p><strong>Note Booking :</strong> {{ $scraped['rating'] ?? '-' }}</p>
            <h5>Images :</h5>
            <div class="row">
                @if(isset($scraped['images']) && is_array($scraped['images']) && count($scraped['images']))
                    @foreach($scraped['images'] as $img)
                        <div class="col-md-3 mb-3">
                            <img src="{{ $img }}" alt="image" class="img-fluid rounded border" style="max-height: 200px; object-fit: cover;">
                        </div>
                    @endforeach
                @else
                    <div class="col-12">Aucune image trouvée.</div>
                @endif
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Services associés</div>
        <div class="card-body">
            @if(!empty($services))
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Type de service</th>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Prix</th>
                            <th>Capacité</th>
                            <th>Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($services as $service)
                            <tr>
                                <td>{{ $service->type_service ?? '-' }}</td>
                                <td>{{ $service->date_service ?? '-' }}</td>
                                <td>{{ $service->description ?? '-' }}</td>
                                <td>{{ $service->prix ?? '-' }} €</td>
                                <td>{{ $service->capacite ?? '-' }}</td>
                                <td>{{ $service->type ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="alert alert-warning">Aucun service associé.</div>
            @endif
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
    

    <a href="{{ route('mes.offres') }}" class="btn btn-secondary">
        Retour à toutes mes offres
    </a>
</div>
@endsection