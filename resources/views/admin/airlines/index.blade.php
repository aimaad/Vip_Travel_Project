@extends("Layout::admin.app")

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Compagnies Aériennes</h3>
            <a href="{{ route('admin.airlines.create') }}" class="btn btn-primary">
                <i class="icon ion-ios-add"></i> Ajouter une compagnie
            </a>
        </div>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Logo</th>
                        <th>Code IATA</th>
                        <th>Nom</th>
                        <th>Domaine</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($airlines as $airline)
                    <tr>
                        <td class="text-center">
                            <img src="{{ $airline->logo_url }}" 
                                 alt="{{ $airline->name }}"
                                 class="img-thumbnail"
                                 style="max-height: 50px;">
                        </td>
                        <td>{{ $airline->iata_code }}</td>
                        <td>{{ $airline->name }}</td>
                        <td>{{ $airline->domain }}</td>
                        <td>
                            <form action="{{ route('admin.airlines.destroy', $airline->id) }}" method="POST" style="display: inline-block;" 
                                onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette compagnie ?');">
                              @csrf
                              @method('DELETE')
                              <button type="submit" class="btn btn-sm btn-danger">
                                <i class="icon ion-ios-trash"></i>                              </button>
                          </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection