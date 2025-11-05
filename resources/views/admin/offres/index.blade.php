@extends('admin.layouts.app')

@section('content')
<div class="container my-4">
    <h2 class="mb-4 text-primary fw-bold">
        <i class="fa fa-list"></i> Liste des offres
    </h2>

    @if(session('success'))
        <div class="alert alert-success shadow-sm mb-3">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger shadow-sm mb-3">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nom hôtel</th>
                            <th>Créée par</th>
                            <th>Statut</th>
                            <th>Date création</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($offres as $offre)
                            <tr>
                                <td>{{ $offre->id }}</td>
                                <td>{{ $offre->hotel_scraping->hotel_name ?? '—' }}</td>
                                <td>{{ $offre->creator->name ?? '' }} {{ $offre->creator->prenom ?? '' }}</td>
                                <td>
                                    <span class="badge 
                                        @if($offre->statut === 'valide') bg-success
                                        @elseif($offre->statut === 'refusee') bg-danger
                                        @elseif($offre->statut === 'brouillon') bg-warning text-dark
                                        @else bg-secondary
                                        @endif
                                    ">
                                        {{ ucfirst($offre->statut) }}
                                    </span>
                                </td>
                                <td>{{ $offre->created_at ? $offre->created_at->format('d/m/Y H:i') : '-' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.offres.validation', $offre->id) }}" class="btn btn-primary btn-sm">
                                        <i class="fa fa-eye"></i> Voir
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">Aucune offre trouvée.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4">
        {{ $offres->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection