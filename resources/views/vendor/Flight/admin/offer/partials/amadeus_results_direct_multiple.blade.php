<div class="flight-results" x-data="{ editing: false }">
    
    {{-- Bouton pour basculer le mode --}}
    <div class="mb-3">
        <button class="btn btn-sm btn-primary" @click="editing = !editing" x-text="editing ? 'Mode lecture' : 'Mode édition'"></button>
    </div>
    {{-- Vols Aller --}}
    @if(!empty($amadeusResults['flights']))
        <h3 class="text-primary mb-4">Vols Aller</h3>
        @foreach($amadeusResults['flights'] as $index => $flightGroup)
            @php
                $hasValidFlight = false;
                foreach ($flightGroup as $offer) {
                    if (isset($offer['itineraries'][0]['segments'][0])) {
                        $hasValidFlight = true;
                        break;
                    }
                }
            @endphp

            @if($hasValidFlight)
                <div class="flight-card border mb-4 p-4 rounded shadow-sm">
                    @foreach($flightGroup as $subIndex => $offer)
                        @if(isset($offer['itineraries'][0]['segments'][0]))
                            @php
                                $segment = $offer['itineraries'][0]['segments'][0];
                                $airline = \App\Models\Airline::where('iata_code', $segment['carrierCode'])->first();
                                $logoUrl = $airline
                                    ? 'https://logo.clearbit.com/'.$airline->domain.'?size=150'
                                    : 'https://via.placeholder.com/100?text='.$segment['carrierCode'];
                                $airlineName = $airline->name ?? $segment['carrierCode'];
                            @endphp

                            @if($subIndex > 0)
                                <div class="direct-arrow text-center my-4">
                                    <i class="fas fa-arrow-right text-success"></i>
                                    <span class="text-muted small">Direct</span>
                                </div>
                            @endif

                            <div class="d-flex align-items-center">
                                <img src="{{ $logoUrl }}" alt="{{ $airlineName }}" class="airline-logo me-3">
                                <div>
                                    <template x-if="!editing">
                                        <h5 class="mb-1 text-primary">{{ $airlineName }}</h5>
                                    </template>
                                    <template x-if="editing">
                                        <input type="text" value="{{ $airlineName }}" class="form-control">
                                    </template>

                                    <template x-if="!editing">
                                        <small class="text-muted">Vol {{ $segment['carrierCode'] }} {{ $segment['number'] }}</small>
                                    </template>
                                    <template x-if="editing">
                                        <input type="text" value="{{ $segment['carrierCode'] }} {{ $segment['number'] }}" class="form-control">
                                    </template>
                                </div>
                            </div>

                            <div class="timeline d-flex justify-content-between align-items-center mt-3">
                                <div class="text-center">
                                    <template x-if="!editing">
                                        <strong>{{ $segment['departure']['iataCode'] }}</strong>
                                    </template>
                                    <template x-if="editing">
                                        <input type="text" value="{{ $segment['departure']['iataCode'] }}" class="form-control">
                                    </template>

                                    <template x-if="!editing">
                                        <p class="mb-0 small text-muted">{{ \Carbon\Carbon::parse($segment['departure']['at'])->format('d/m/Y H:i') }}</p>
                                    </template>
                                    <template x-if="editing">
                                        <input type="datetime-local" value="{{ \Carbon\Carbon::parse($segment['departure']['at'])->format('Y-m-d\TH:i') }}" class="form-control">
                                    </template>
                                </div>

                                <div class="timeline-line flex-grow-1 position-relative mx-3">
                                    <span class="text-success small position-absolute top-50 start-50 translate-middle">Direct</span>
                                    <div class="line bg-success"></div>
                                </div>

                                <div class="text-center">
                                    <template x-if="!editing">
                                        <strong>{{ $segment['arrival']['iataCode'] }}</strong>
                                    </template>
                                    <template x-if="editing">
                                        <input type="text" value="{{ $segment['arrival']['iataCode'] }}" class="form-control">
                                    </template>

                                    <template x-if="!editing">
                                        <p class="mb-0 small text-muted">{{ \Carbon\Carbon::parse($segment['arrival']['at'])->format('d/m/Y H:i') }}</p>
                                    </template>
                                    <template x-if="editing">
                                        <input type="datetime-local" value="{{ \Carbon\Carbon::parse($segment['arrival']['at'])->format('Y-m-d\TH:i') }}" class="form-control">
                                    </template>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                <div class="alert alert-info">
                    Le {{ $loop->iteration }}{{ $loop->iteration == 1 ? 'er' : 'e' }} vol aller n'existe pas.
                </div>
            @endif
        @endforeach
    @else
        <div class="alert alert-warning">Aucun vol aller trouvé.</div>
    @endif

    {{-- Vols Retour --}}
    @if(!empty($amadeusResults['return_flights']))
        <h3 class="text-primary mb-4">Vols Retour</h3>
        @foreach($amadeusResults['return_flights'] as $index => $returnGroup)
            @php
                $hasValidFlight = false;
                foreach ($returnGroup as $offer) {
                    if (isset($offer['itineraries'][0]['segments'][0])) {
                        $hasValidFlight = true;
                        break;
                    }
                }
            @endphp

            @if($hasValidFlight)
                <div class="flight-card border mb-4 p-4 rounded shadow-sm">
                    @foreach($returnGroup as $subIndex => $offer)
                        @if(isset($offer['itineraries'][0]['segments'][0]))
                            @php
                                $segment = $offer['itineraries'][0]['segments'][0];
                                $airline = \App\Models\Airline::where('iata_code', $segment['carrierCode'])->first();
                                $logoUrl = $airline
                                    ? 'https://logo.clearbit.com/'.$airline->domain.'?size=150'
                                    : 'https://via.placeholder.com/100?text='.$segment['carrierCode'];
                                $airlineName = $airline->name ?? $segment['carrierCode'];
                            @endphp

                            @if($subIndex > 0)
                                <div class="direct-arrow text-center my-4">
                                    <i class="fas fa-arrow-right text-success"></i>
                                    <span class="text-muted small">Direct</span>
                                </div>
                            @endif

                            <div class="d-flex align-items-center">
                                <img src="{{ $logoUrl }}" alt="{{ $airlineName }}" class="airline-logo me-3">
                                <div>
                                    <template x-if="!editing">
                                        <h5 class="mb-1 text-primary">{{ $airlineName }}</h5>
                                    </template>
                                    <template x-if="editing">
                                        <input type="text" value="{{ $airlineName }}" class="form-control">
                                    </template>

                                    <template x-if="!editing">
                                        <small class="text-muted">Vol {{ $segment['carrierCode'] }} {{ $segment['number'] }}</small>
                                    </template>
                                    <template x-if="editing">
                                        <input type="text" value="{{ $segment['carrierCode'] }} {{ $segment['number'] }}" class="form-control">
                                    </template>
                                </div>
                            </div>

                            <div class="timeline d-flex justify-content-between align-items-center mt-3">
                                <div class="text-center">
                                    <template x-if="!editing">
                                        <strong>{{ $segment['departure']['iataCode'] }}</strong>
                                    </template>
                                    <template x-if="editing">
                                        <input type="text" value="{{ $segment['departure']['iataCode'] }}" class="form-control">
                                    </template>

                                    <template x-if="!editing">
                                        <p class="mb-0 small text-muted">{{ \Carbon\Carbon::parse($segment['departure']['at'])->format('d/m/Y H:i') }}</p>
                                    </template>
                                    <template x-if="editing">
                                        <input type="datetime-local" value="{{ \Carbon\Carbon::parse($segment['departure']['at'])->format('Y-m-d\TH:i') }}" class="form-control">
                                    </template>
                                </div>

                                <div class="timeline-line flex-grow-1 position-relative mx-3">
                                    <span class="text-success small position-absolute top-50 start-50 translate-middle">Direct</span>
                                    <div class="line bg-success"></div>
                                </div>

                                <div class="text-center">
                                    <template x-if="!editing">
                                        <strong>{{ $segment['arrival']['iataCode'] }}</strong>
                                    </template>
                                    <template x-if="editing">
                                        <input type="text" value="{{ $segment['arrival']['iataCode'] }}" class="form-control">
                                    </template>

                                    <template x-if="!editing">
                                        <p class="mb-0 small text-muted">{{ \Carbon\Carbon::parse($segment['arrival']['at'])->format('d/m/Y H:i') }}</p>
                                    </template>
                                    <template x-if="editing">
                                        <input type="datetime-local" value="{{ \Carbon\Carbon::parse($segment['arrival']['at'])->format('Y-m-d\TH:i') }}" class="form-control">
                                    </template>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                <div class="alert alert-info">
                    Le {{ $loop->iteration }}{{ $loop->iteration == 1 ? 'er' : 'e' }} vol retour n'existe pas.
                </div>
            @endif
        @endforeach
    @else
        <div class="alert alert-warning">Aucun vol retour trouvé.</div>
    @endif
</div>

<style>
    .flight-card {
        padding: 1.5rem;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .airline-logo {
        max-width: 50px;
        max-height: 50px;
        object-fit: contain;
    }

    .timeline-line .line {
        height: 2px;
        background-color: #dee2e6;
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        z-index: 0;
    }

    .direct-arrow {
        margin-top: 1rem;
    }
</style>
