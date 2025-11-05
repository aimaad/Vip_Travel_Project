<div class="multi-flight-results">
    @if(!empty($amadeusResults['offers']))
        <div class="card shadow border-0 mb-4">
            <div class="card-header bg-gradient-primary text-white">
                <h4 class="mb-0" style="color: black ;">
                    <i class="icon ion-ios-airplane"></i> Details
                </h4>
            </div>
            <div class="card-body bg-light">
                @foreach($amadeusResults['offers'] as $offerIndex => $offer)
                    <div class="mb-4">
                        <h5 class="text-info fw-bold">
                            <i class="icon ion-ios-paper"></i> Offer {{ $offerIndex + 1 }}
                        </h5>

                        {{-- Outbound Flights --}}
                        @if(isset($offer['outbound']) && !empty($offer['outbound']))
                            <div class="mb-4">
                                <h6 class="text-primary fw-bold mb-3">
                                    <i class="icon ion-ios-airplane"></i> Outbound Flights
                                </h6>
                                @foreach($offer['outbound'] as $legIndex => $outboundLeg)
                                    <div class="route-container mb-4">
                                        <div class="route-line">
                                            @foreach($outboundLeg as $flight)
                                                @foreach($flight['itineraries'] as $itinerary)
                                                    @foreach($itinerary['segments'] as $segment)
                                                        @php
                                                            $carrierCode = $segment['carrierCode'] ?? 'N/A';
                                                            $airline = \App\Models\Airline::where('iata_code', $carrierCode)->first();
                                                            $logoUrl = $airline 
                                                                ? 'https://logo.clearbit.com/'.$airline->domain.'?size=150' 
                                                                : 'https://via.placeholder.com/100?text='.$carrierCode;
                                                            $airlineName = $airline->name ?? $carrierCode;

                                                            $departureDate = \Carbon\Carbon::parse($segment['departure']['at'] ?? now())->format('d/m/Y H:i');
                                                            $arrivalDate = \Carbon\Carbon::parse($segment['arrival']['at'] ?? now())->format('d/m/Y H:i');

                                                            $isDirect = $itinerary['segments'] && count($itinerary['segments']) === 1;
                                                        @endphp
                                                        <div class="route-segment">
                                                            <div class="d-flex align-items-center mb-3">
                                                                <img src="{{ $logoUrl }}" alt="{{ $airlineName }}" class="airline-logo me-3">
                                                                <div>
                                                                    <h6 class="fw-bold text-dark mb-0">
                                                                        {{ $airlineName }}
                                                                    </h6>
                                                                    <small class="text-muted">Flight {{ $segment['carrierCode'] }} {{ $segment['flightNumber'] ?? 'N/A' }}</small>
                                                                </div>
                                                            </div>
                                                            <div class="route-info">
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <div class="text-center">
                                                                        <strong>Departure</strong>
                                                                        <p>{{ $segment['departure']['iataCode'] ?? 'N/A' }}<br>
                                                                            {{ $departureDate }}</p>
                                                                    </div>
                                                                    <div class="icon-container">
                                                                        <i class="icon ion-ios-airplane text-primary"></i>
                                                                        @if($isDirect)
                                                                            <span class="badge bg-success">Direct</span>
                                                                        @endif
                                                                    </div>
                                                                    <div class="text-center">
                                                                        <strong>Arrival</strong>
                                                                        <p>{{ $segment['arrival']['iataCode'] ?? 'N/A' }}<br>
                                                                            {{ $arrivalDate }}</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endforeach
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="icon ion-alert-circled"></i> No outbound flights found.
                            </div>
                        @endif

                        {{-- Return Flights --}}
                        @if(isset($offer['return']) && !empty($offer['return']))
                            <div>
                                <h6 class="text-success fw-bold mb-3">
                                    <i class="icon ion-ios-airplane"></i> Return Flights
                                </h6>
                                @foreach($offer['return'] as $legIndex => $returnLeg)
                                    <div class="route-container mb-4">
                                        <div class="route-line">
                                            @foreach($returnLeg as $flight)
                                                @foreach($flight['itineraries'] as $itinerary)
                                                    @foreach($itinerary['segments'] as $segment)
                                                        @php
                                                            $carrierCode = $segment['carrierCode'] ?? 'N/A';
                                                            $airline = \App\Models\Airline::where('iata_code', $carrierCode)->first();
                                                            $logoUrl = $airline 
                                                                ? 'https://logo.clearbit.com/'.$airline->domain.'?size=150' 
                                                                : 'https://via.placeholder.com/100?text='.$carrierCode;
                                                            $airlineName = $airline->name ?? $carrierCode;

                                                            $departureDate = \Carbon\Carbon::parse($segment['departure']['at'] ?? now())->format('d/m/Y H:i');
                                                            $arrivalDate = \Carbon\Carbon::parse($segment['arrival']['at'] ?? now())->format('d/m/Y H:i');

                                                            $isDirect = $itinerary['segments'] && count($itinerary['segments']) === 1;
                                                        @endphp
                                                        <div class="route-segment">
                                                            <div class="d-flex align-items-center mb-3">
                                                                <img src="{{ $logoUrl }}" alt="{{ $airlineName }}" class="airline-logo me-3">
                                                                <div>
                                                                    <h6 class="fw-bold text-dark mb-0">
                                                                        {{ $airlineName }}
                                                                    </h6>
                                                                    <small class="text-muted">Flight {{ $segment['carrierCode'] }} {{ $segment['flightNumber'] ?? 'N/A' }}</small>
                                                                </div>
                                                            </div>
                                                            <div class="route-info">
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <div class="text-center">
                                                                        <strong>Departure</strong>
                                                                        <p>{{ $segment['departure']['iataCode'] ?? 'N/A' }}<br>
                                                                            {{ $departureDate }}</p>
                                                                    </div>
                                                                    <div class="icon-container">
                                                                        <i class="icon ion-ios-airplane text-success"></i>
                                                                        @if($isDirect)
                                                                            <span class="badge bg-success">Direct</span>
                                                                        @endif
                                                                    </div>
                                                                    <div class="text-center">
                                                                        <strong>Arrival</strong>
                                                                        <p>{{ $segment['arrival']['iataCode'] ?? 'N/A' }}<br>
                                                                            {{ $arrivalDate }}</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endforeach
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="icon ion-alert-circled"></i> No return flights found.
                            </div>
                        @endif
                    </div>

                    <hr class="my-4">
                @endforeach
            </div>
        </div>
    @else
        <div class="alert alert-warning shadow border-0">
            <i class="icon ion-alert-circled"></i> No flight offers found matching your criteria.
        </div>
    @endif
</div>

<style>
    .airline-logo {
        width: 50px;
        height: 50px;
        object-fit: contain;
    }

    .route-container {
        position: relative;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 8px;
        background-color: #fff;
    }

    .route-line {
        border-left: 3px solid #007bff;
        padding-left: 20px;
    }

    .route-segment {
        margin-bottom: 20px;
    }

    .icon-container {
        display: flex;
        align-items: center;
        flex-direction: column;
    }

    .icon-container i {
        font-size: 24px;
    }

    .badge {
        margin-top: 5px;
    }
</style>