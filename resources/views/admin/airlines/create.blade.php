@extends("Layout::admin.app")
@section('content')
<div class="card">
    <div class="card-header">
        <h3>Ajouter une compagnie</h3>
    </div>
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="airline-form" action="{{ route('admin.airlines.store') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Code IATA (2 lettres)</label>
                        <input type="text" name="iata_code" 
                               class="form-control @error('iata_code') is-invalid @enderror" 
                               value="{{ old('iata_code') }}" 
                               required maxlength="2">
                        @error('iata_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nom complet</label>
                        <input type="text" name="name" 
                               class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name') }}" 
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label>Domaine (ex: airfrance.com)</label>
                <input type="text" name="domain" 
                       class="form-control @error('domain') is-invalid @enderror" 
                       value="{{ old('domain') }}" 
                       required>
                @error('domain')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Utilisé pour générer le logo via Clearbit</small>
            </div>
            
            <div class="form-group text-center my-4">
                <div id="logo-preview" class="mb-3" style="display: none;">
                    <img id="logo-image" src="" alt="Logo preview" class="img-thumbnail" style="max-height: 100px;">
                </div>
                <button type="button" id="preview-logo" class="btn btn-secondary">
                    <i class="icon ion-ios-eye"></i> Prévisualiser le logo
                </button>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="icon ion-ios-save"></i> Enregistrer
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion de la prévisualisation du logo
    const previewBtn = document.getElementById('preview-logo');
    if (previewBtn) {
        previewBtn.addEventListener('click', function() {
            const domain = document.querySelector('input[name="domain"]').value.trim();
            if (!domain) {
                alert("Veuillez entrer un domaine valide (ex: airfrance.com)");
                return;
            }

            const logoUrl = `https://logo.clearbit.com/${domain}?size=150`;
            const logoImage = document.getElementById('logo-image');
            
            // Test de chargement de l'image
            const testImage = new Image();
            testImage.onload = function() {
                logoImage.src = logoUrl;
                document.getElementById('logo-preview').style.display = 'block';
            };
            testImage.onerror = function() {
                alert("Logo non trouvé pour ce domaine");
            };
            testImage.src = logoUrl;
        });
    }

    // Vérification en temps réel des doublons
    const domainInput = document.querySelector('input[name="domain"]');
    const iataInput = document.querySelector('input[name="iata_code"]');
    
    function checkExisting(field, value) {
        if (!value || value.length < (field === 'iata_code' ? 2 : 5)) return;
        
        fetch(`/admin/airlines/check-existing?${field}=${encodeURIComponent(value)}`)
            .then(response => response.json())
            .then(data => {
                const input = document.querySelector(`input[name="${field}"]`);
                const feedback = input.nextElementSibling;
                
                if (data.exists) {
                    input.classList.add('is-invalid');
                    if (!feedback || !feedback.classList.contains('invalid-feedback')) {
                        const newFeedback = document.createElement('div');
                        newFeedback.className = 'invalid-feedback';
                        newFeedback.textContent = field === 'iata_code' 
                            ? 'Ce code IATA existe déjà' 
                            : 'Ce domaine est déjà utilisé';
                        input.after(newFeedback);
                    }
                } else {
                    input.classList.remove('is-invalid');
                    if (feedback && feedback.classList.contains('invalid-feedback')) {
                        feedback.remove();
                    }
                }
            });
    }
    
    if (domainInput) {
        domainInput.addEventListener('blur', function() {
            checkExisting('domain', this.value);
        });
    }
    
    if (iataInput) {
        iataInput.addEventListener('blur', function() {
            checkExisting('iata_code', this.value);
        });
    }
});
</script>
@endpush