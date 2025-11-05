@extends('admin.layouts.app')

@section('content')
<div class="container my-4">
    <h2 class="mb-4 text-primary fw-bold">
        <i class="fa fa-check-circle"></i> Validation de l'offre
    </h2>

    @if(session('success'))
        <div class="alert alert-success shadow-sm mb-3">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger shadow-sm mb-3">{{ session('error') }}</div>
    @endif

    <div class="card mb-4 shadow" style="border-radius: 1rem;">
        <div class="card-header bg-gradient bg-primary text-white d-flex justify-content-between align-items-center" style="border-radius: 1rem 1rem 0 0;">
            <div>
                <strong>
                    <i class="fa fa-building"></i>
                    Offre #{{ $offre->id }} — {{ $offre->hotel_scraping->hotel_name ?? '' }}
                </strong>
            </div>
            <span class="badge fs-6
                @if($offre->statut === 'valide') bg-success
                @elseif($offre->statut === 'refusee') bg-danger
                @else bg-warning text-dark @endif
            ">
                {{ ucfirst($offre->statut) }}
            </span>
        </div>
        <div class="card-body bg-light" style="border-radius: 0 0 1rem 1rem;">

            {{-- Affichage des infos scrappées --}}
            @if($offre->hotel_scraping)
                <div class="mb-4 p-3 bg-white rounded shadow-sm border">
                    <h5 class="mb-3 text-secondary"><i class="fa fa-info-circle"></i> Informations scrappées de l'hôtel</h5>
                    <div class="row">
                        <div class="col-md-7">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <i class="fa fa-building"></i>
                                    <strong>Nom :</strong>
                                    <span class="text-primary">{{ $offre->hotel_scraping->hotel_name }}</span>
                                </li>
                                <li class="mb-2">
                                    <i class="fa fa-map-marker"></i>
                                    <strong>Adresse :</strong>
                                    {{ $offre->hotel_scraping->address ?? 'Non disponible' }}
                                </li>
                                <li class="mb-2">
                                    <i class="fa fa-star text-warning"></i>
                                    <strong>Note :</strong>
                                    {{ $offre->hotel_scraping->rating ?? 'Non disponible' }}
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-5">
                            @php
                                $images = $offre->hotel_scraping->images;
                                if (is_string($images)) {
                                    $images = json_decode($images, true);
                                }
                                $images = is_array($images) ? $images : [];
                            @endphp
                            @if(count($images) > 0)
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($images as $img)
                                        <a href="#" class="popup-img-link" data-img="{{ $img }}">
                                            <img src="{{ $img }}" alt="Image hôtel" class="rounded shadow" style="width: 80px; height: 65px; object-fit: cover; transition: transform .2s;">
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <div class="mb-3">
                <strong><i class="fa fa-user-circle"></i> Créée par :</strong>
                <span class="text-secondary">{{ $offre->creator->name ?? 'N/A' }} {{ $offre->creator->prenom ?? '' }}</span>
            </div>

            <hr>

            <h5 class="mt-4 mb-2"><i class="fa fa-door-open"></i> Détails des chambres</h5>
            <div class="bg-white rounded p-3 mb-4 shadow-sm border">
                @if(is_string($offre->room_types))
                    @php $roomTypes = json_decode($offre->room_types, true); @endphp
                @else
                    @php $roomTypes = $offre->room_types; @endphp
                @endif

                @if(is_array($roomTypes) && count($roomTypes))
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Type</th>
                                    <th>Adultes</th>
                                    <th>Enfants</th>
                                    <th>Kids</th>
                                    <th>Bébés</th>
                                    <th>Prix</th>
                                    <th>Chambres dispo.</th>
                                    <th>Pension</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($roomTypes as $room)
                                    <tr>
                                        <td>{{ $room['type'] ?? '' }}</td>
                                        <td>{{ $room['adults'] ?? '' }}</td>
                                        <td>{{ $room['children'] ?? '' }}</td>
                                        <td>{{ $room['kids'] ?? '' }}</td>
                                        <td>{{ $room['babies'] ?? '' }}</td>
                                        <td>{{ $room['price'] ?? '' }}</td>
                                        <td>{{ $room['available_rooms'] ?? '' }}</td>
                                        <td>{{ $room['pension'] ?? '' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <em>Aucune chambre renseignée.</em>
                @endif
            </div>

            <h5 class="mb-2"><i class="fa fa-concierge-bell"></i> Services</h5>
            <div class="bg-white rounded p-3 mb-4 shadow-sm border">
                @php
                    $services = $offre->services;
                    if (is_string($services)) {
                        $services = json_decode($services, true);
                    } elseif (is_object($services) && method_exists($services, 'toArray')) {
                        $services = $services->toArray();
                    }
                    $services = is_array($services) ? $services : [];
                @endphp
                @if(count($services))
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Type</th>
                                    <th>Description</th>
                                    <th>Date</th>
                                    <th>Prix</th>
                                    <th>Capacité</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($services as $service)
                                    <tr>
                                        <td>{{ $service['type_service'] ?? $service['type'] ?? '' }}</td>
                                        <td>{{ $service['description'] ?? '' }}</td>
                                        <td>{{ $service['date_service'] ?? '-' }}</td>
                                        <td>
                                            <span class="text-success">{{ $service['prix'] ?? '-' }} €</span>
                                        </td>
                                        <td>{{ $service['capacite'] ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <em>Aucun service associé.</em>
                @endif
            </div>
       
            <h5 class="mb-3 text-primary"><i class="fa fa-plane-departure"></i> Détails des Vols</h5>

            @foreach($offre->offerFlights as $flight)
                <div class="bg-white p-4 rounded shadow-sm border mb-4">
                    <h6 class="text-dark fw-bold mb-3">
                        <i class="fa fa-plane"></i> 
                        <small class="text-muted">(type : {{ ucfirst($flight->flight_type) }})</small>
                    </h6>
            
                    {{-- Tarification --}}
                    <div class="row text-center mb-3">
                        <div class="col">
                            <div class="fw-bold text-secondary"><i class="fa fa-user text-primary"></i> Adulte</div>
                            <div class="text-success fs-5">{{ number_format($flight->price_adult, 2) }} €</div>
                        </div>
                        <div class="col">
                            <div class="fw-bold text-secondary"><i class="fa fa-child text-primary"></i> Enfant</div>
                            <div class="text-success fs-5">{{ number_format($flight->price_child, 2) }} €</div>
                        </div>
                        <div class="col">
                            <div class="fw-bold text-secondary"><i class="fa fa-baby text-primary"></i> Bébé</div>
                            <div class="text-success fs-5">{{ number_format($flight->price_baby, 2) }} €</div>
                        </div>
                        <div class="col">
                            <div class="fw-bold text-secondary"><i class="fa fa-chair text-primary"></i> Places dispo</div>
                            <div class="text-dark fs-5">{{ $flight->places }}</div>
                        </div>
                    </div>
            
                    {{-- Étapes du vol --}}
                    @foreach($flight->flightLegs as $leg)
                        <div class="border rounded p-3 mb-3">
                            <h6 class="text-secondary fw-bold">
                                <i class="fa fa-location-arrow"></i> {{ ucfirst($leg->direction) }}
                            </h6>
                            <ul class="list-unstyled mb-0">
                                <li><strong>Vol :</strong> {{ $leg->flight_number }}</li>
                                <li><strong>Compagnie :</strong>
                                    @if($leg->airline_logo)
                                        <img src="{{ $leg->airline_logo }}" style="height: 24px; vertical-align: middle;">
                                    @else
                                        <span class="text-muted">Non renseignée</span>
                                    @endif
                                </li>
                                <li><strong>Départ :</strong> {{ $leg->departure_city }} — {{ $leg->departure_date }} à {{ $leg->departure_time }}</li>
                                <li><strong>Arrivée :</strong> {{ $leg->arrival_city }} — {{ $leg->arrival_date }} à {{ $leg->arrival_time }}</li>
                            </ul>
                        </div>
                    @endforeach
                </div>
            @endforeach
            

            

            <div class="d-flex gap-3 mt-3">
                @if($offre->statut !== 'valide')
                    <form action="{{ route('admin.offres.valider', $offre->id) }}" method="post" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success shadow-sm px-4">
                            <i class="fa fa-check-circle"></i> Valider l'offre
                        </button>
                    </form>
                @endif

                @if($offre->statut !== 'refusee')
    <form action="{{ route('admin.offres.refuser', $offre->id) }}" method="post" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de refuser cette offre ?');">
        @csrf
        <div class="mb-2">
            <textarea name="refus_commentaire" class="form-control" placeholder="Motif du refus (sera envoyé à l’utilisateur)" required rows="2"></textarea>
        </div>
        <button type="submit" class="btn btn-danger shadow-sm px-4 ms-2">
            <i class="fa fa-times-circle"></i> Refuser l'offre
        </button>
    </form>
@endif
            </div>
        </div>
    </div>
    <a href="{{ route('admin.offres.index') }}" class="btn btn-secondary shadow-sm">
        <i class="fa fa-arrow-left"></i> Retour à la liste
    </a>
</div>

{{-- Popup Modal for image --}}
<div id="popup-image-modal" class="modal fade" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content bg-transparent border-0">
      <div class="modal-body text-center p-0">
        <img src="" alt="Agrandissement" id="popup-image" class="img-fluid rounded shadow" style="max-height:80vh;">
      </div>
      <button type="button" class="btn-close position-absolute end-0 top-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.popup-img-link').forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            var imgSrc = this.getAttribute('data-img');
            var modalImg = document.getElementById('popup-image');
            modalImg.src = imgSrc;
            var modal = new bootstrap.Modal(document.getElementById('popup-image-modal'));
            modal.show();
        });
    });
});
</script>
@endpush