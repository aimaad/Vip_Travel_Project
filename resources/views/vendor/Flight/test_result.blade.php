@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2>Test Résultat Recherche Vol #{{ $result->id }}</h2>
    <div class="mb-3">
        <strong>Status :</strong> {{ $result->status }}<br>
        <strong>Places :</strong> {{ $result->places }}<br>
        <strong>Prix Adulte :</strong> {{ $result->price_adult }}<br>
        <strong>Prix Enfant :</strong> {{ $result->price_child }}<br>
        <strong>Prix Bébé :</strong> {{ $result->price_baby }}<br>
    </div>
    <hr>
    <div>
        {!! $result->results_html !!}
    </div>
</div>
@endsection