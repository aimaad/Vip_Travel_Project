<div x-data="{ editModeId: null }" class="flight-results">
    @if(isset($amadeusResults['data']) && !empty($amadeusResults['data'][0]['data']))
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="icon ion-ios-airplane"></i> Flight Offers</h4>
            </div>
            <div class="card-body p-0">
                @php
                $groupedFlights = [];
                $includedAirlines = isset($request->included_airline_codes)
                    ? array_map('strtoupper', array_map('trim', explode(',', $request->included_airline_codes)))
                    : null;

                foreach ($amadeusResults['data'][0]['data'] as $offer) {
                    $carrierCode = $offer['validatingAirlineCodes'][0] ?? $offer['itineraries'][0]['segments'][0]['carrierCode'];
                    if ($includedAirlines && !in_array($carrierCode, $includedAirlines)) continue;

                    $isNonStop = true;
                    foreach ($offer['itineraries'] as $itinerary) {
                        if (count($itinerary['segments']) > 1) {
                            $isNonStop = false;
                            break;
                        }
                    }
                    if (!$isNonStop) continue;

                    $outboundKey = $offer['itineraries'][0]['segments'][0]['departure']['at'];
                    $groupedFlights[$outboundKey]['outbound'] = $offer['itineraries'][0];
                    $groupedFlights[$outboundKey]['return_options'][] = $offer['itineraries'][1] ?? null;
                    $groupedFlights[$outboundKey]['price'] = $offer['price'];
                    $groupedFlights[$outboundKey]['details'] = $offer;

                    $adultPrice = $childPrice = $infantPrice = 0;
                    foreach ($offer['travelerPricings'] as $pricing) {
                        if ($pricing['travelerType'] === 'ADULT') $adultPrice = $pricing['price']['total'];
                        elseif ($pricing['travelerType'] === 'CHILD') $childPrice = $pricing['price']['total'];
                        elseif ($pricing['travelerType'] === 'HELD_INFANT') $infantPrice = $pricing['price']['total'];
                    }
                    $groupedFlights[$outboundKey]['pricing'] = ['adult'=>$adultPrice,'child'=>$childPrice,'infant'=>$infantPrice];

                    $airline = \App\Models\Airline::where('iata_code', $carrierCode)->first();
                    $logoUrl = $airline
                        ? 'https://logo.clearbit.com/'.$airline->domain.'?size=150'
                        : 'https://via.placeholder.com/100?text='.$carrierCode;
                    $airlineName = $airline->name ?? $carrierCode;

                    $groupedFlights[$outboundKey]['logoUrl'] = $logoUrl;
                    $groupedFlights[$outboundKey]['airlineName'] = $airlineName;
                }
                @endphp

                @if(empty($groupedFlights))
                    <div class="alert alert-warning text-center m-4">
                        <i class="icon ion-alert-circled"></i> No flights found matching your criteria.
                    </div>
                @else
                    @foreach($groupedFlights as $outboundKey => $flightGroup)
                        @php
                            $departureOutbound = \Carbon\Carbon::parse($flightGroup['outbound']['segments'][0]['departure']['at']);
                            $arrivalOutbound = \Carbon\Carbon::parse($flightGroup['outbound']['segments'][0]['arrival']['at']);
                        @endphp

                        <div class="flight-group border-bottom p-4"
                             x-data="{
                                editing: false,
                                outbound: @js([
                                    'airlineName' => $flightGroup['airlineName'],
                                    'carrierCode' => $flightGroup['outbound']['segments'][0]['carrierCode'],
                                    'flightNumber' => $flightGroup['outbound']['segments'][0]['number'],
                                    'departureIata' => $flightGroup['outbound']['segments'][0]['departure']['iataCode'],
                                    'departureDate' => $departureOutbound->format('Y-m-d'),
                                    'departureTime' => $departureOutbound->format('H:i'),
                                    'arrivalIata' => $flightGroup['outbound']['segments'][0]['arrival']['iataCode'],
                                    'arrivalDate' => $arrivalOutbound->format('Y-m-d'),
                                    'arrivalTime' => $arrivalOutbound->format('H:i'),
                                    'logoUrl' => $flightGroup['logoUrl'],
                                ]),
                                returnOptions: @js(
                                    collect($flightGroup['return_options'])
                                        ->filter(fn($ro) => $ro && count($ro['segments']) === 1)
                                        ->map(function($ro) {
                                            $airline = \App\Models\Airline::where('iata_code', $ro['segments'][0]['carrierCode'])->first();
                                            return [
                                                'airlineName' => $airline->name ?? $ro['segments'][0]['carrierCode'],
                                                'carrierCode' => $ro['segments'][0]['carrierCode'],
                                                'flightNumber' => $ro['segments'][0]['number'],
                                                'departureIata' => $ro['segments'][0]['departure']['iataCode'],
                                                'departureDate' => \Carbon\Carbon::parse($ro['segments'][0]['departure']['at'])->format('Y-m-d'),
                                                'departureTime' => \Carbon\Carbon::parse($ro['segments'][0]['departure']['at'])->format('H:i'),
                                                'arrivalIata' => $ro['segments'][0]['arrival']['iataCode'],
                                                'arrivalDate' => \Carbon\Carbon::parse($ro['segments'][0]['arrival']['at'])->format('Y-m-d'),
                                                'arrivalTime' => \Carbon\Carbon::parse($ro['segments'][0]['arrival']['at'])->format('H:i'),
                                                'logoUrl' => $airline
                                                    ? "https://logo.clearbit.com/{$airline->domain}?size=150"
                                                    : "https://via.placeholder.com/100?text={$ro['segments'][0]['carrierCode']}",
                                            ];
                                        })
                                        ->values()
                                ),
                               updateAirline() {
                                const iataPrefix = this.outbound.carrierCode.slice(0, 2).toUpperCase();
                                if(iataPrefix.length < 2) return; // éviter les requêtes inutiles
                            
                                fetch(`/api/airline/${iataPrefix}`)
                                    .then(res => res.json())
                                    .then(data => {
                                        if (data && data.name && data.logoUrl) {
                                            this.outbound.airlineName = data.name;
                                            this.outbound.logoUrl = data.logoUrl;
                                        } else {
                                            this.outbound.airlineName = iataPrefix;
                                            this.outbound.logoUrl = `https://via.placeholder.com/100?text=${iataPrefix}`;
                                        }
                                    })
                                    .catch(err => console.error(err));
                            }
                            ,
                            cancelEdit() {
                                this.outbound = JSON.parse(JSON.stringify(this.originalOutbound));
                                this.returnOptions = JSON.parse(JSON.stringify(this.originalReturnOptions));
                                this.editing = false;
                            },
                            
                            updateReturnAirline(idx) {
                                const iataPrefix = this.returnOptions[idx].carrierCode.slice(0, 2).toUpperCase();
                                if(iataPrefix.length < 2) return;
                            
                                fetch(`/api/airline/${iataPrefix}`)
                                    .then(res => res.json())
                                    .then(data => {
                                        if(data && data.name && data.logoUrl) {
                                            this.returnOptions[idx].airlineName = data.name;
                                            this.returnOptions[idx].logoUrl = data.logoUrl;
                                        } else {
                                            this.returnOptions[idx].airlineName = iataPrefix;
                                            this.returnOptions[idx].logoUrl = `https://via.placeholder.com/100?text=${iataPrefix}`;
                                        }
                                    });
                            }
,                            
toggleEdit() {
    this.originalOutbound = JSON.parse(JSON.stringify(this.outbound));
    this.originalReturnOptions = JSON.parse(JSON.stringify(this.returnOptions));
    this.editing = true;
},
                                saveChanges() {
                                    fetch('{{ route('flights.updateDirectSingle') }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        },
                                        body: JSON.stringify({
                                            flightId: '{{ $outboundKey }}',
                                            outbound: this.outbound,
                                            returnOptions: this.returnOptions
                                        })
                                    }).then(res => res.json())
                                      .then(data => {
                                          alert('Modifications sauvegardées !');
                                          this.editing = false;
                                      }).catch(err => {
                                          console.error(err);
                                          alert('Erreur lors de la sauvegarde');
                                      });
                                },
                             }"
                        >
                               <!-- Vol Aller -->
<div class="row align-items-center mb-4">
    <div class="col-md-8">
        <div class="d-flex align-items-center mb-3">
            <img :src="outbound.logoUrl" alt="Logo" class="me-3" style="width:50px;height:50px;object-fit:contain;">
            <div>
                <template x-if="!editing">
                    <h5 class="mb-0 text-primary"><span x-text="outbound.airlineName"></span></h5>
                </template>
                <template x-if="editing">
                    <input type="text" class="form-control form-control-sm mb-1" x-model="outbound.airlineName" />
                </template>

                <small class="text-muted">
                    Flight
                    <template x-if="!editing">
                        <span x-text="outbound.carrierCode + ' ' + outbound.flightNumber"></span>
                    </template>
                    <template x-if="editing" class="d-flex gap-1">
                        <input type="text" class="form-control form-control-sm w-auto" x-model="outbound.carrierCode"  @input="updateAirline()"/>
                        <input type="text" class="form-control form-control-sm w-auto" x-model="outbound.flightNumber"/>
                    </template>
                </small>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <!-- Départ -->
            <div class="text-center">
                <div class="fw-bold fs-5">
                    <template x-if="!editing">
                        <span x-text="outbound.departureTime"></span>
                    </template>
                    <template x-if="editing">
                        <input type="time" class="form-control form-control-sm" x-model="outbound.departureTime" />
                    </template>
                </div>
                <div class="text-muted small">
                    <template x-if="!editing">
                        <span x-text="outbound.departureIata"></span>
                    </template>
                    <template x-if="editing">
                        <input type="text" class="form-control form-control-sm" x-model="outbound.departureIata" />
                    </template>
                </div>
                <div class="text-muted small">
                    <template x-if="!editing">
                        <span x-text="new Date(outbound.departureDate).toLocaleDateString('fr-FR')"></span>
                    </template>
                    <template x-if="editing">
                        <input type="date" class="form-control form-control-sm" x-model="outbound.departureDate" />
                    </template>
                </div>
            </div>

            <div class="flex-grow-1 d-flex align-items-center justify-content-center text-success">
                <small>Direct</small>
            </div>

            <!-- Arrivée -->
            <div class="text-center">
                <div class="fw-bold fs-5">
                    <template x-if="!editing">
                        <span x-text="outbound.arrivalTime"></span>
                    </template>
                    <template x-if="editing">
                        <input type="time" class="form-control form-control-sm" x-model="outbound.arrivalTime" />
                    </template>
                </div>
                <div class="text-muted small">
                    <template x-if="!editing">
                        <span x-text="outbound.arrivalIata"></span>
                    </template>
                    <template x-if="editing">
                        <input type="text" class="form-control form-control-sm" x-model="outbound.arrivalIata" />
                    </template>
                </div>
                <div class="text-muted small">
                    <template x-if="!editing">
                        <span x-text="new Date(outbound.arrivalDate).toLocaleDateString('fr-FR')"></span>
                    </template>
                    <template x-if="editing">
                        <input type="date" class="form-control form-control-sm" x-model="outbound.arrivalDate" />
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Prix et actions -->
    <div class="col-md-4 d-flex flex-column justify-content-between border-start ps-4">
        
        <div class="mt-3 text-end">
            <template x-if="!editing">
                <button class="btn btn-sm btn-outline-primary" @click="toggleEdit()">Modifier</button>
            </template>
            <template x-if="editing">
                <div class="d-flex gap-2 justify-content-end mt-3">
                    <button class="btn btn-sm btn-success" @click="saveChanges()">Sauvegarder</button>
                    <button type="button" class="btn btn-sm btn-secondary" @click="cancelEdit()">Annuler</button>
                </div>
            </template>
            
        </div>
    </div>
</div>


                        <!-- Vol Retour -->
<template x-for="(ret, idx) in returnOptions" :key="idx">
    <div class="return-option-card p-3 bg-light rounded mb-3">
        <div class="row align-items-center">

            <!-- Logo + Compagnie -->
            <div class="col-md-4 text-center">
                <img :src="ret.logoUrl" alt="Logo" class="mb-2" style="width:50px;height:50px;object-fit:contain;">
                <template x-if="!editing">
                    <h6 class="text-primary mb-0"><span x-text="ret.airlineName"></span></h6>
                </template>
                <template x-if="editing">
                    <input type="text" class="form-control form-control-sm mb-1" x-model="ret.airlineName" />
                </template>

                <small class="text-muted">
                    Flight
                    <template x-if="!editing">
                        <span x-text="ret.carrierCode + ' ' + ret.flightNumber"></span>
                    </template>
                    <template x-if="editing" class="d-flex gap-1">
                        <input type="text" class="form-control form-control-sm w-auto" x-model="ret.carrierCode"  @input="updateReturnAirline(idx)"/>
                        <input type="text" class="form-control form-control-sm w-auto" x-model="ret.flightNumber" />
                    </template>
                </small>
            </div>

            <!-- Départ -->
            <div class="col-md-4 text-center">
                <div class="fw-bold">
                    <template x-if="!editing">
                        <span x-text="ret.departureTime"></span>
                    </template>
                    <template x-if="editing">
                        <input type="time" class="form-control form-control-sm" x-model="ret.departureTime" />
                    </template>
                </div>
                <div class="text-muted small">
                    <template x-if="!editing">
                        <span x-text="ret.departureIata"></span>
                    </template>
                    <template x-if="editing">
                        <input type="text" class="form-control form-control-sm" x-model="ret.departureIata" />
                    </template>
                </div>
                <div class="text-muted small">
                    <template x-if="!editing">
                        <span x-text="new Date(ret.departureDate).toLocaleDateString('fr-FR')"></span>
                    </template>
                    <template x-if="editing">
                        <input type="date" class="form-control form-control-sm" x-model="ret.departureDate" />
                    </template>
                </div>
            </div>

            <!-- Arrivée -->
            <div class="col-md-4 text-center">
                <div class="fw-bold">
                    <template x-if="!editing">
                        <span x-text="ret.arrivalTime"></span>
                    </template>
                    <template x-if="editing">
                        <input type="time" class="form-control form-control-sm" x-model="ret.arrivalTime" />
                    </template>
                </div>
                <div class="text-muted small">
                    <template x-if="!editing">
                        <span x-text="ret.arrivalIata"></span>
                    </template>
                    <template x-if="editing">
                        <input type="text" class="form-control form-control-sm" x-model="ret.arrivalIata" />
                    </template>
                </div>
                <div class="text-muted small">
                    <template x-if="!editing">
                        <span x-text="new Date(ret.arrivalDate).toLocaleDateString('fr-FR')"></span>
                    </template>
                    <template x-if="editing">
                        <input type="date" class="form-control form-control-sm" x-model="ret.arrivalDate" />
                    </template>
                </div>
            </div>

        </div>
    </div>
</template>

                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    @else
        <div class="alert alert-warning shadow-sm text-center">
            <i class="icon ion-alert-circled"></i> No flights found matching your criteria.
        </div>
    @endif
</div>

<style>
.flight-card { padding: 1rem; background-color: #fff; border-radius: 8px; }
.airline-logo { max-width: 50px; max-height: 50px; object-fit: contain; }
.return-option-card { transition: all 0.2s ease; }
.return-option-card:hover { background-color: #f8f9fa !important; transform: translateY(-2px); }
</style>
