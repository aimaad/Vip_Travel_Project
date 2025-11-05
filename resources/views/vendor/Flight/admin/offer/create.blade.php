@extends('admin.layouts.app')

@section('content')
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
       
    </div>
@endif
@if(session('warning'))
    <div class="alert alert-warning">
        {{ session('warning') }}
       
    </div>
@endif
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{__("Create New Flight Offer")}}</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card card-primary card-outline">
                                <div class="card-body box-profile">
                                    <h4 class="text-center">{{__("Offer Type")}}</h4>
                                    <ul class="list-group list-group-unbordered mb-3" id="offer-type-list">
                                        @foreach(['direct_single', 'direct_multiple', 'multi_flight_single', 'multi_flight_multiple'] as $type)
                                        <li class="list-group-item p-0">
                                            <button type="button" 
                                                    data-type="{{ $type }}" 
                                                    class="offer-type-link w-100 text-left btn btn-link @if(request('type') === $type) active @endif">
                                                <b>{{ __(ucfirst(str_replace('_', ' ', $type))) }}</b>
                                                <p class="text-muted mb-0">
                                                    @if($type === 'direct_single')
                                                        {{__("One offer with one direct flight")}}
                                                    @elseif($type === 'direct_multiple')
                                                        {{__("Multiple offers with direct flights")}}
                                                    @elseif($type === 'multi_flight_single')
                                                        {{__("One offer with multiple flights")}}
                                                    @else
                                                        {{__("Multiple offers with multiple flights")}}
                                                    @endif
                                                </p>
                                            </button>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div id="offer-form-container">
                                @if(request()->has('type'))
                                    <div class="text-center p-4">
                                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                                        <p>{{ __("Loading form...") }}</p>
                                    </div>
                                @else
                                    <div class="alert alert-info">
                                        {{__("Please select an offer type from the left menu to continue")}}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .offer-type-link {
        cursor: pointer;
        display: block;
        padding: 10px;
        color: #333;
        text-decoration: none;
        background: none;
        border: none;
        text-align: left;
    }
    .offer-type-link:hover {
        background-color: #f8f9fa;
    }
    .offer-type-link.active {
        background-color: #e9ecef;
        font-weight: bold;
    }
    #offer-form-container {
        min-height: 300px;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Debug initial
  

    // Fonction améliorée pour charger le formulaire
    function loadOfferForm(type) {
        
        // Mise à jour visuelle
        $('.offer-type-link').removeClass('active');
        $(`.offer-type-link[data-type="${type}"]`).addClass('active');
        
        // Mise à jour URL
        const newUrl = `{{ route('flight.admin.offers.create') }}?type=${type}`;
        history.pushState(null, '', newUrl);
        
        // Affichage loader
        $('#offer-form-container').html(`
            <div class="text-center p-4">
                <i class="fas fa-spinner fa-spin fa-2x"></i>
                <p>{{ __("Loading form...") }}</p>
            </div>
        `);

        // Requête AJAX avec gestion d'erreur améliorée
        $.ajax({
            url: "{{ route('flight.admin.offers.get_form') }}",
            type: 'POST',
            data: {
                type: type,
                _token: "{{ csrf_token() }}"
            },
            dataType: 'html', // Important pour la réponse HTML
            success: function(response) {
                $('#offer-form-container').html(response);
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                let errorMsg = "{{ __('Error loading form. Please try again.') }}";
                
                if (xhr.status === 404) {
                    errorMsg = "{{ __('Form not found. Contact support.') }}";
                }
                
                $('#offer-form-container').html(`
                    <div class="alert alert-danger">
                        ${errorMsg}
                        <br><small>Error: ${xhr.status} - ${error}</small>
                        <button onclick="window.location.reload()" class="btn btn-sm btn-default mt-2">
                            {{ __('Reload Page') }}
                        </button>
                    </div>
                `);
            }
        });
    }

    // Chargement initial
    @if(request()->has('type'))
        loadOfferForm("{{ request('type') }}");
    @endif

    // Gestion des clics
    $(document).on('click', '.offer-type-link', function(e) {
        e.preventDefault();
        const type = $(this).data('type');
        loadOfferForm(type);
    });
});
</script>
@endpush