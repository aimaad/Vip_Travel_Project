<div class="multi-flight-results">
    <?php if(!empty($amadeusResults['offers'])): ?>
        <div class="card shadow border-0 mb-4">
            <div class="card-header bg-gradient-primary text-white">
                <h4 class="mb-0" style="color: black ;">
                    <i class="icon ion-ios-airplane"></i> Details
                </h4>
            </div>
            <div class="card-body bg-light">
                <?php $__currentLoopData = $amadeusResults['offers']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $offerIndex => $offer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="mb-4">
                        <h5 class="text-info fw-bold">
                            <i class="icon ion-ios-paper"></i> Offer <?php echo e($offerIndex + 1); ?>

                        </h5>

                        
                        <?php if(isset($offer['outbound']) && !empty($offer['outbound'])): ?>
                            <div class="mb-4">
                                <h6 class="text-primary fw-bold mb-3">
                                    <i class="icon ion-ios-airplane"></i> Outbound Flights
                                </h6>
                                <?php $__currentLoopData = $offer['outbound']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $legIndex => $outboundLeg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="route-container mb-4">
                                        <div class="route-line">
                                            <?php $__currentLoopData = $outboundLeg; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $flight): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php $__currentLoopData = $flight['itineraries']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $itinerary): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php $__currentLoopData = $itinerary['segments']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $segment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php
                                                            $carrierCode = $segment['carrierCode'] ?? 'N/A';
                                                            $airline = \App\Models\Airline::where('iata_code', $carrierCode)->first();
                                                            $logoUrl = $airline 
                                                                ? 'https://logo.clearbit.com/'.$airline->domain.'?size=150' 
                                                                : 'https://via.placeholder.com/100?text='.$carrierCode;
                                                            $airlineName = $airline->name ?? $carrierCode;

                                                            $departureDate = \Carbon\Carbon::parse($segment['departure']['at'] ?? now())->format('d/m/Y H:i');
                                                            $arrivalDate = \Carbon\Carbon::parse($segment['arrival']['at'] ?? now())->format('d/m/Y H:i');

                                                            $isDirect = $itinerary['segments'] && count($itinerary['segments']) === 1;
                                                        ?>
                                                        <div class="route-segment">
                                                            <div class="d-flex align-items-center mb-3">
                                                                <img src="<?php echo e($logoUrl); ?>" alt="<?php echo e($airlineName); ?>" class="airline-logo me-3">
                                                                <div>
                                                                    <h6 class="fw-bold text-dark mb-0">
                                                                        <?php echo e($airlineName); ?>

                                                                    </h6>
                                                                    <small class="text-muted">Flight <?php echo e($segment['carrierCode']); ?> <?php echo e($segment['flightNumber'] ?? 'N/A'); ?></small>
                                                                </div>
                                                            </div>
                                                            <div class="route-info">
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <div class="text-center">
                                                                        <strong>Departure</strong>
                                                                        <p><?php echo e($segment['departure']['iataCode'] ?? 'N/A'); ?><br>
                                                                            <?php echo e($departureDate); ?></p>
                                                                    </div>
                                                                    <div class="icon-container">
                                                                        <i class="icon ion-ios-airplane text-primary"></i>
                                                                        <?php if($isDirect): ?>
                                                                            <span class="badge bg-success">Direct</span>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                    <div class="text-center">
                                                                        <strong>Arrival</strong>
                                                                        <p><?php echo e($segment['arrival']['iataCode'] ?? 'N/A'); ?><br>
                                                                            <?php echo e($arrivalDate); ?></p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="icon ion-alert-circled"></i> No outbound flights found.
                            </div>
                        <?php endif; ?>

                        
                        <?php if(isset($offer['return']) && !empty($offer['return'])): ?>
                            <div>
                                <h6 class="text-success fw-bold mb-3">
                                    <i class="icon ion-ios-airplane"></i> Return Flights
                                </h6>
                                <?php $__currentLoopData = $offer['return']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $legIndex => $returnLeg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="route-container mb-4">
                                        <div class="route-line">
                                            <?php $__currentLoopData = $returnLeg; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $flight): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php $__currentLoopData = $flight['itineraries']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $itinerary): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php $__currentLoopData = $itinerary['segments']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $segment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php
                                                            $carrierCode = $segment['carrierCode'] ?? 'N/A';
                                                            $airline = \App\Models\Airline::where('iata_code', $carrierCode)->first();
                                                            $logoUrl = $airline 
                                                                ? 'https://logo.clearbit.com/'.$airline->domain.'?size=150' 
                                                                : 'https://via.placeholder.com/100?text='.$carrierCode;
                                                            $airlineName = $airline->name ?? $carrierCode;

                                                            $departureDate = \Carbon\Carbon::parse($segment['departure']['at'] ?? now())->format('d/m/Y H:i');
                                                            $arrivalDate = \Carbon\Carbon::parse($segment['arrival']['at'] ?? now())->format('d/m/Y H:i');

                                                            $isDirect = $itinerary['segments'] && count($itinerary['segments']) === 1;
                                                        ?>
                                                        <div class="route-segment">
                                                            <div class="d-flex align-items-center mb-3">
                                                                <img src="<?php echo e($logoUrl); ?>" alt="<?php echo e($airlineName); ?>" class="airline-logo me-3">
                                                                <div>
                                                                    <h6 class="fw-bold text-dark mb-0">
                                                                        <?php echo e($airlineName); ?>

                                                                    </h6>
                                                                    <small class="text-muted">Flight <?php echo e($segment['carrierCode']); ?> <?php echo e($segment['flightNumber'] ?? 'N/A'); ?></small>
                                                                </div>
                                                            </div>
                                                            <div class="route-info">
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <div class="text-center">
                                                                        <strong>Departure</strong>
                                                                        <p><?php echo e($segment['departure']['iataCode'] ?? 'N/A'); ?><br>
                                                                            <?php echo e($departureDate); ?></p>
                                                                    </div>
                                                                    <div class="icon-container">
                                                                        <i class="icon ion-ios-airplane text-success"></i>
                                                                        <?php if($isDirect): ?>
                                                                            <span class="badge bg-success">Direct</span>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                    <div class="text-center">
                                                                        <strong>Arrival</strong>
                                                                        <p><?php echo e($segment['arrival']['iataCode'] ?? 'N/A'); ?><br>
                                                                            <?php echo e($arrivalDate); ?></p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="icon ion-alert-circled"></i> No return flights found.
                            </div>
                        <?php endif; ?>
                    </div>

                    <hr class="my-4">
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-warning shadow border-0">
            <i class="icon ion-alert-circled"></i> No flight offers found matching your criteria.
        </div>
    <?php endif; ?>
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
</style><?php /**PATH C:\angular\Vip_travel\Vip_Travel_Project\resources\views/vendor/Flight/admin/offer/partials/amadeus_results_multi_flight_multiple.blade.php ENDPATH**/ ?>