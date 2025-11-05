<div class="flight-results">
    <?php if(!empty($amadeusResults)): ?>
        <div class="card shadow border-0 mb-4">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">
                    <i class="fas fa-plane-departure me-2"></i> Flight Details
                </h4>
            </div>
            <div class="card-body bg-light">
                <?php $__currentLoopData = $amadeusResults; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $offerIndex => $offer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="mb-4">
                        <h5 class="text-info fw-bold">
                            <i class="fas fa-ticket-alt me-2"></i> Offer <?php echo e($offerIndex + 1); ?>

                        </h5>

                        
                        <?php if(isset($offer['outbound']) && !empty($offer['outbound'])): ?>
                            <div class="mb-4">
                                <h6 class="text-primary fw-bold mb-3">
                                    <i class="fas fa-plane-departure me-2"></i> Outbound Flights
                                </h6>
                                <?php $__currentLoopData = $offer['outbound']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $flightKey => $flightOffer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $itineraries = $flightOffer['itineraries'] ?? [];
                                    ?>

                                    <?php $__currentLoopData = $itineraries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $itinerary): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $segments = $itinerary['segments'] ?? [];
                                            $outboundSegment = $segments[0] ?? null;
                                            $carrierCode = $outboundSegment['carrierCode'] ?? 'N/A';
                                            $airline = \App\Models\Airline::where('iata_code', $carrierCode)->first();
                                            $logoUrl = $airline 
                                                ? 'https://logo.clearbit.com/'.$airline->domain.'?size=150' 
                                                : 'https://via.placeholder.com/100?text='.$carrierCode;
                                            $airlineName = $airline->name ?? $carrierCode;
                                        ?>

                                        <?php if($outboundSegment): ?>
                                            <div class="card mb-3 shadow-sm border-0">
                                                <div class="card-body bg-white">
                                                    <div class="d-flex align-items-center mb-3">
                                                        <img src="<?php echo e($logoUrl); ?>" alt="<?php echo e($airlineName); ?>" class="airline-logo me-3">
                                                        <div>
                                                            <h6 class="fw-bold text-dark mb-0">
                                                                <?php echo e($airlineName); ?>

                                                            </h6>
                                                            <small class="text-muted">Flight <?php echo e($outboundSegment['carrierCode']); ?> <?php echo e($outboundSegment['number']); ?></small>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 text-muted">
                                                            <strong>Departure:</strong>
                                                            <p class="mb-0">
                                                                City: <?php echo e($outboundSegment['departure']['iataCode'] ?? 'N/A'); ?> <br>
                                                                Date: <?php echo e(\Carbon\Carbon::parse($outboundSegment['departure']['at'] ?? now())->format('d/m/Y H:i')); ?>

                                                            </p>
                                                        </div>
                                                        <div class="col-md-6 text-muted">
                                                            <strong>Arrival:</strong>
                                                            <p class="mb-0">
                                                                City: <?php echo e($outboundSegment['arrival']['iataCode'] ?? 'N/A'); ?> <br>
                                                                Date: <?php echo e(\Carbon\Carbon::parse($outboundSegment['arrival']['at'] ?? now())->format('d/m/Y H:i')); ?>

                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">No outbound flights found.</div>
                        <?php endif; ?>

                        
                        <?php if(isset($offer['return']) && !empty($offer['return'])): ?>
                            <div>
                                <h6 class="text-success fw-bold mb-3">
                                    <i class="fas fa-plane-arrival me-2"></i> Return Flights
                                </h6>
                                <?php $__currentLoopData = $offer['return']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $flightKey => $flightOffer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $itineraries = $flightOffer['itineraries'] ?? [];
                                    ?>

                                    <?php $__currentLoopData = $itineraries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $itinerary): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $segments = $itinerary['segments'] ?? [];
                                            $returnSegment = $segments[0] ?? null;
                                            $carrierCode = $returnSegment['carrierCode'] ?? 'N/A';
                                            $airline = \App\Models\Airline::where('iata_code', $carrierCode)->first();
                                            $logoUrl = $airline 
                                                ? 'https://logo.clearbit.com/'.$airline->domain.'?size=150' 
                                                : 'https://via.placeholder.com/100?text='.$carrierCode;
                                            $airlineName = $airline->name ?? $carrierCode;
                                        ?>

                                        <?php if($returnSegment): ?>
                                            <div class="card mb-3 shadow-sm border-0">
                                                <div class="card-body bg-white">
                                                    <div class="d-flex align-items-center mb-3">
                                                        <img src="<?php echo e($logoUrl); ?>" alt="<?php echo e($airlineName); ?>" class="airline-logo me-3">
                                                        <div>
                                                            <h6 class="fw-bold text-dark mb-0">
                                                                <?php echo e($airlineName); ?>

                                                            </h6>
                                                            <small class="text-muted">Flight <?php echo e($returnSegment['carrierCode']); ?> <?php echo e($returnSegment['number']); ?></small>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 text-muted">
                                                            <strong>Departure:</strong>
                                                            <p class="mb-0">
                                                                City: <?php echo e($returnSegment['departure']['iataCode'] ?? 'N/A'); ?> <br>
                                                                Date: <?php echo e(\Carbon\Carbon::parse($returnSegment['departure']['at'] ?? now())->format('d/m/Y H:i')); ?>

                                                            </p>
                                                        </div>
                                                        <div class="col-md-6 text-muted">
                                                            <strong>Arrival:</strong>
                                                            <p class="mb-0">
                                                                City: <?php echo e($returnSegment['arrival']['iataCode'] ?? 'N/A'); ?> <br>
                                                                Date: <?php echo e(\Carbon\Carbon::parse($returnSegment['arrival']['at'] ?? now())->format('d/m/Y H:i')); ?>

                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">No return flights found.</div>
                        <?php endif; ?>
                    </div>

                    <hr class="my-4">
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-warning shadow border-0">
            <i class="fas fa-exclamation-triangle me-2"></i> No flight offers found matching your criteria.
        </div>
    <?php endif; ?>
</div>

<style>
    .airline-logo {
        width: 50px;
        height: 50px;
        object-fit: contain;
    }

    .flight-results .card {
        border-radius: 12px;
    }

    .flight-results h6,
    .flight-results p {
        font-size: 0.95rem;
    }
</style><?php /**PATH C:\angular\Vip_travel\Vip_Travel_Project\resources\views/vendor/Flight/admin/offer/partials/amadeus_results_multi_flight_single.blade.php ENDPATH**/ ?>