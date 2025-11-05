@extends('layouts.app')
@section('content')
<div class="container py-4" style="padding-top: 105px !important;">
    <h2 class="mb-4">Nos offres</h2>
    <div class="row">
        @foreach($offres as $offre)
            @php $scraping = $offre->hotel_scraping; @endphp
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm h-100">
                    @if(!empty($scraping->images[0]))
                        <img src="{{ $scraping->images[0] }}" class="card-img-top" style="height:180px;object-fit:cover;">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $scraping->hotel_name ?? 'Offre' }}</h5>
                        <p class="card-text">{{ $scraping->address ?? '' }}</p>
                        <a href="{{ route('offres.show', $offre->id) }}" class="btn btn-primary">Voir l’offre</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="mt-4 d-flex justify-content-center">
        {{ $offres->links() }}
    </div>
</div>
@endsection