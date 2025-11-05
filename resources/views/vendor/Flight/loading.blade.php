@extends('layouts.app')

@section('content')
<div class="container py-5 text-center">
    <h2 class="mb-4">Recherche en cours...</h2>

    <div id="loader" class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Chargement...</span>
    </div>

    <p class="mt-3" id="message">Merci de patienter, nous recherchons les meilleures offres de vols.</p>
</div>

<script>
    const searchId = {{ $searchId }};
    const messageElement = document.getElementById('message');
    const loader = document.getElementById('loader');

    async function checkStatus() {
        try {
            const res = await fetch("{{ route('api.flight.results.status', ['id' => $searchId]) }}");
            const data = await res.json();

            if (data.status === 'done') {
                window.location.href = "{{ url('/flight-results') }}/" + searchId;
            } else if (data.status === 'error') {
                loader.classList.remove('spinner-border');
                messageElement.innerHTML = '<div class="alert alert-danger">Une erreur est survenue pendant la recherche. Veuillez réessayer plus tard.</div>';
            } else if (data.status === 'not_found') {
                loader.classList.remove('spinner-border');
                messageElement.innerHTML = '<div class="alert alert-warning">Résultat introuvable.</div>';
            } else {
                setTimeout(checkStatus, 3000);
            }
        } catch (e) {
            loader.classList.remove('spinner-border');
            messageElement.innerHTML = '<div class="alert alert-danger">Une erreur réseau est survenue. Merci de recharger la page.</div>';
        }
    }

    setTimeout(checkStatus, 3000);
</script>
@endsection
