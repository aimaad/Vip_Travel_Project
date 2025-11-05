@extends('admin.layouts.app')

@section('content')
<style>
    .table-modern th, .table-modern td { vertical-align: middle !important; }
    .table-modern tbody tr { transition: box-shadow 0.2s, transform 0.2s; }
    .table-modern tbody tr:hover { box-shadow: 0 2px 12px rgba(60, 60, 150, 0.11); transform: scale(1.01); background: #f9faff; }
    .actions-group .btn { margin-right: 4px; margin-bottom: 2px; }
    @media (max-width: 576px) {
        .table-responsive { font-size: 0.95rem; }
        .actions-group .btn { width: 100%; margin-bottom: 6px;}
    }
</style>
<div class="container py-4">
    <h2 class="mb-4"><i class="bi bi-list-task"></i> Mes Offres</h2>
    @if($offres->isEmpty())
        <div class="alert alert-warning shadow-sm">Aucune offre trouvée.</div>
    @else
    <div class="table-responsive">
        <table class="table table-modern align-middle table-hover shadow-sm rounded">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Nom hôtel</th>
                    <th>Statut</th>
                    <th>Date création</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($offres as $offre)
                    <tr>
                        <td><span class="fw-bold">{{ $offre->id }}</span></td>
                        <td>{{ optional(\App\Models\HotelScraping::find($offre->hotel_scraping_id))->hotel_name ?? '-' }}</td>
                        <td>
                            @switch($offre->statut)
                                @case('brouillon')
                                    <span class="badge bg-secondary"><i class="bi bi-pencil-square"></i> Brouillon</span>
                                    @break
                                @case('valide')
                                    <span class="badge bg-success"><i class="bi bi-check-circle"></i> Validée</span>
                                    @break
                                @case('refusee')
                                    <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Refusée</span>
                                    @break
                                @case('archivee')
                                    <span class="badge bg-dark"><i class="bi bi-archive"></i> Archivée</span>
                                    @break
                                @case('arretee')
                                    <span class="badge bg-warning text-dark"><i class="bi bi-stop-circle"></i> Arrêtée</span>
                                    @break
                                @default
                                    <span class="badge bg-light text-dark">{{ $offre->statut ?? '-' }}</span>
                            @endswitch
                        </td>
                        <td><span class="text-muted">{{ \Carbon\Carbon::parse($offre->created_at)->format('d/m/Y H:i') }}</span></td>
                        <td>
                            <div class="actions-group d-flex flex-wrap justify-content-center">
                            @if($offre->statut === 'brouillon')
                                <a href="{{ route('offre.duplicate', $offre->id) }}" class="btn btn-outline-secondary btn-sm" title="Dupliquer">
                                    <i class="bi bi-files"></i>
                                </a>
                                <a href="{{ route('offre.edit', $offre->id) }}" class="btn btn-outline-primary btn-sm" title="Modifier">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('offre.archive', $offre->id) }}" method="POST" style="display:inline;">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-outline-dark btn-sm" type="submit" title="Archiver" onclick="return confirm('Archiver cette offre ?')">
                                        <i class="bi bi-archive"></i>
                                    </button>
                                </form>
                            @elseif($offre->statut === 'valide')
                                <a href="{{ route('offre.duplicate', $offre->id) }}" class="btn btn-outline-secondary btn-sm" title="Dupliquer">
                                    <i class="bi bi-files"></i>
                                </a>
                                <form action="{{ route('offre.stop', $offre->id) }}" method="POST" style="display:inline;">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-outline-warning btn-sm" type="submit" title="Arrêter" onclick="return confirm('Arrêter cette offre ?')">
                                        <i class="bi bi-stop-circle"></i>
                                    </button>
                                </form>
                                <form action="{{ route('offre.archive', $offre->id) }}" method="POST" style="display:inline;">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-outline-dark btn-sm" type="submit" title="Archiver" onclick="return confirm('Archiver cette offre ?')">
                                        <i class="bi bi-archive"></i>
                                    </button>
                                </form>
                                <a href="{{ route('offre.detail', $offre->id) }}" class="btn btn-outline-success btn-sm" title="Voir détail">
                                    <i class="bi bi-eye"></i>
                                </a>
                            @elseif($offre->statut === 'refusee')
                                <a href="{{ route('offre.refus.detail', $offre->id) }}" class="btn btn-outline-danger btn-sm" title="Voir le refus">
                                    <i class="bi bi-eye-slash"></i>
                                </a>
                            @else
                                <a href="{{ route('offre.detail', $offre->id) }}" class="btn btn-outline-info btn-sm" title="Voir détail">
                                    <i class="bi bi-eye"></i>
                                </a>
                            @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
        <div class="d-flex justify-content-center">
            {{ $offres->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
@endsection