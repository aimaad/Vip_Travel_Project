<div class="flight-results">
    <?php if(isset($amadeusResults['data']) && !empty($amadeusResults['data'][0]['data'])): ?>
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="icon ion-ios-airplane"></i> Flight Offers
                    </h4>
                </div>
            </div>
            <div class="card-body p-0">
                <?php
                // Group flights by departure time
                $groupedFlights = [];
                $includedAirlines = isset($request->included_airline_codes) 
                    ? array_map('strtoupper', array_map('trim', explode(',', $request->included_airline_codes)))
                    : null;

                foreach ($amadeusResults['data'][0]['data'] as $offer) {
                    $carrierCode = $offer['validatingAirlineCodes'][0] ?? $offer['itineraries'][0]['segments'][0]['carrierCode'];

                    if ($includedAirlines && !in_array($carrierCode, $includedAirlines)) {
                        continue;
                    }

                    $isNonStop = true;
                    foreach ($offer['itineraries'] as $itinerary) {
                        if (count($itinerary['segments']) > 1) {
                            $isNonStop = false;
                            break;
                        }
                    }

                    if ($isNonStop) {
                        $outboundKey = $offer['itineraries'][0]['segments'][0]['departure']['at'];
                        $groupedFlights[$outboundKey]['outbound'] = $offer['itineraries'][0];
                        $groupedFlights[$outboundKey]['return_options'][] = $offer['itineraries'][1] ?? null;
                        $groupedFlights[$outboundKey]['price'] = $offer['price'];
                        $groupedFlights[$outboundKey]['details'] = $offer;

                        $adultPrice = 0;
                        $childPrice = 0;
                        $infantPrice = 0;

                        foreach ($offer['travelerPricings'] as $pricing) {
                            if ($pricing['travelerType'] === 'ADULT') {
                                $adultPrice = $pricing['price']['total'];
                            } elseif ($pricing['travelerType'] === 'CHILD') {
                                $childPrice = $pricing['price']['total'];
                            } elseif ($pricing['travelerType'] === 'HELD_INFANT') {
                                $infantPrice = $pricing['price']['total'];
                            }
                        }

                        $groupedFlights[$outboundKey]['pricing'] = [
                            'adult' => $adultPrice,
                            'child' => $childPrice,
                            'infant' => $infantPrice
                        ];

                        $airline = \App\Models\Airline::where('iata_code', $carrierCode)->first();
                        $logoUrl = $airline 
                            ? 'https://logo.clearbit.com/'.$airline->domain.'?size=150' 
                            : 'https://via.placeholder.com/100?text='.$carrierCode;
                        $airlineName = $airline->name ?? $carrierCode;

                        $groupedFlights[$outboundKey]['logoUrl'] = $logoUrl;
                        $groupedFlights[$outboundKey]['airlineName'] = $airlineName;
                    }
                }
                ?>

                <?php if(empty($groupedFlights)): ?>
                    <div class="alert alert-warning text-center m-4">
                        <i class="icon ion-alert-circled"></i> No flights found matching your criteria.
                    </div>
                <?php else: ?>
                    <?php $__currentLoopData = $groupedFlights; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $outboundKey => $flightGroup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $detailsId = 'flightDetails' . md5($outboundKey);

                            $departureOutbound = \Carbon\Carbon::parse($flightGroup['outbound']['segments'][0]['departure']['at']);
                            $arrivalOutbound = \Carbon\Carbon::parse($flightGroup['outbound']['segments'][0]['arrival']['at']);
                        ?>

                        <div class="flight-group border-bottom p-4">
                            <div class="row">
                                <!-- Outbound Flight Card -->
                                <div class="col-md-8">
                                    <div class="flight-card mb-3">
                                        <div class="d-flex align-items-center mb-3">
                                            <img src="<?php echo e($flightGroup['logoUrl']); ?>" alt="<?php echo e($flightGroup['airlineName']); ?>" class="airline-logo me-3" style="width: 50px; height: 50px;">
                                            <div>
                                                <h5 class="mb-0 text-primary"><?php echo e($flightGroup['airlineName']); ?></h5>
                                                <small class="text-muted">Flight <?php echo e($flightGroup['outbound']['segments'][0]['carrierCode']); ?> <?php echo e($flightGroup['outbound']['segments'][0]['number']); ?></small>
                                            </div>
                                        </div>

                                        <div class="flight-timeline d-flex justify-content-between align-items-center">
                                            <div class="text-center">
                                                <div class="fw-bold fs-5"><?php echo e($departureOutbound->format('H:i')); ?></div>
                                                <div class="text-muted small"><?php echo e($flightGroup['outbound']['segments'][0]['departure']['iataCode']); ?></div>
                                                <div class="text-muted small"><?php echo e($departureOutbound->format('d/m/Y')); ?></div>
                                            </div>

                                            <div class="flex-grow-1 px-3 text-center">
                                                <div class="small text-success mt-1">Direct</div>
                                            </div>

                                            <div class="text-center">
                                                <div class="fw-bold fs-5"><?php echo e($arrivalOutbound->format('H:i')); ?></div>
                                                <div class="text-muted small"><?php echo e($flightGroup['outbound']['segments'][0]['arrival']['iataCode']); ?></div>
                                                <div class="text-muted small"><?php echo e($arrivalOutbound->format('d/m/Y')); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Price and Action Section -->
                                <div class="col-md-4">
                                    <div class="price-card h-100 d-flex flex-column justify-content-between border-start ps-4">
                                        <div>
                                           
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Return Options Section -->
                            <?php if(!empty($flightGroup['return_options'])): ?>
                                <?php $__currentLoopData = $flightGroup['return_options']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $returnOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($returnOption && count($returnOption['segments']) === 1): ?>
                                        <?php
                                            $carrierCodeReturn = $returnOption['segments'][0]['carrierCode'];
                                            $airlineReturn = \App\Models\Airline::where('iata_code', $carrierCodeReturn)->first();
                                            $logoUrlReturn = $airlineReturn 
                                                ? 'https://logo.clearbit.com/'.$airlineReturn->domain.'?size=150' 
                                                : 'https://via.placeholder.com/100?text='.$carrierCodeReturn;
                                            $airlineNameReturn = $airlineReturn->name ?? $carrierCodeReturn;

                                            $departureReturn = \Carbon\Carbon::parse($returnOption['segments'][0]['departure']['at']);
                                            $arrivalReturn = \Carbon\Carbon::parse($returnOption['segments'][0]['arrival']['at']);
                                        ?>

                                        <div class="return-option-card mt-4 p-3 bg-light rounded">
                                            <div class="row align-items-center">
                                                <div class="col-md-4 text-center">
                                                    <img src="<?php echo e($logoUrlReturn); ?>" alt="<?php echo e($airlineNameReturn); ?>" class="airline-logo mb-2" style="width: 50px; height: 50px;">
                                                    <h6 class="text-primary mb-0"><?php echo e($airlineNameReturn); ?></h6>
                                                    <small class="text-muted">Flight <?php echo e($returnOption['segments'][0]['carrierCode']); ?> <?php echo e($returnOption['segments'][0]['number']); ?></small>
                                                </div>
                                                <div class="col-md-4 text-center">
                                                    <div class="fw-bold"><?php echo e($departureReturn->format('H:i')); ?></div>
                                                    <div class="text-muted small"><?php echo e($returnOption['segments'][0]['departure']['iataCode']); ?></div>
                                                    <div class="text-muted small"><?php echo e($departureReturn->format('d/m/Y')); ?></div>
                                                </div>
                                                <div class="col-md-4 text-center">
                                                    <div class="fw-bold"><?php echo e($arrivalReturn->format('H:i')); ?></div>
                                                    <div class="text-muted small"><?php echo e($returnOption['segments'][0]['arrival']['iataCode']); ?></div>
                                                    <div class="text-muted small"><?php echo e($arrivalReturn->format('d/m/Y')); ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-warning shadow-sm text-center">
            <i class="icon ion-alert-circled"></i> No flights found matching your criteria.
        </div>
    <?php endif; ?>
</div>

<style>
    .flight-card {
        padding: 1rem;
        background-color: #fff;
        border-radius: 8px;
    }

    .price-card {
        padding: 1rem;
    }

    .flight-timeline {
        position: relative;
        padding: 1rem 0;
    }

    .duration-line {
        height: 2px;
        background-color: #dee2e6;
        width: 100%;
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        z-index: 1;
    }

    .airline-logo {
        max-width: 50px;
        max-height: 50px;
        object-fit: contain;
    }

    .return-option-card {
        transition: all 0.2s ease;
    }

    .return-option-card:hover {
        background-color: #f8f9fa !important;
        transform: translateY(-2px);
    }
</style><?php /**PATH C:\angular\Vip_travel\Vip_Travel_Project\resources\views/vendor/Flight/admin/offer/partials/amadeus_results_direct_single.blade.php ENDPATH**/ ?>