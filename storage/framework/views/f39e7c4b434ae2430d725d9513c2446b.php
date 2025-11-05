<div class="flight-results" x-data="{ editing: false }">
    
    
    <div class="mb-3">
        <button class="btn btn-sm btn-primary" @click="editing = !editing" x-text="editing ? 'Mode lecture' : 'Mode édition'"></button>
    </div>
    
    <?php if(!empty($amadeusResults['flights'])): ?>
        <h3 class="text-primary mb-4">Vols Aller</h3>
        <?php $__currentLoopData = $amadeusResults['flights']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $flightGroup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $hasValidFlight = false;
                foreach ($flightGroup as $offer) {
                    if (isset($offer['itineraries'][0]['segments'][0])) {
                        $hasValidFlight = true;
                        break;
                    }
                }
            ?>

            <?php if($hasValidFlight): ?>
                <div class="flight-card border mb-4 p-4 rounded shadow-sm">
                    <?php $__currentLoopData = $flightGroup; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subIndex => $offer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if(isset($offer['itineraries'][0]['segments'][0])): ?>
                            <?php
                                $segment = $offer['itineraries'][0]['segments'][0];
                                $airline = \App\Models\Airline::where('iata_code', $segment['carrierCode'])->first();
                                $logoUrl = $airline
                                    ? 'https://logo.clearbit.com/'.$airline->domain.'?size=150'
                                    : 'https://via.placeholder.com/100?text='.$segment['carrierCode'];
                                $airlineName = $airline->name ?? $segment['carrierCode'];
                            ?>

                            <?php if($subIndex > 0): ?>
                                <div class="direct-arrow text-center my-4">
                                    <i class="fas fa-arrow-right text-success"></i>
                                    <span class="text-muted small">Direct</span>
                                </div>
                            <?php endif; ?>

                            <div class="d-flex align-items-center">
                                <img src="<?php echo e($logoUrl); ?>" alt="<?php echo e($airlineName); ?>" class="airline-logo me-3">
                                <div>
                                    <template x-if="!editing">
                                        <h5 class="mb-1 text-primary"><?php echo e($airlineName); ?></h5>
                                    </template>
                                    <template x-if="editing">
                                        <input type="text" value="<?php echo e($airlineName); ?>" class="form-control">
                                    </template>

                                    <template x-if="!editing">
                                        <small class="text-muted">Vol <?php echo e($segment['carrierCode']); ?> <?php echo e($segment['number']); ?></small>
                                    </template>
                                    <template x-if="editing">
                                        <input type="text" value="<?php echo e($segment['carrierCode']); ?> <?php echo e($segment['number']); ?>" class="form-control">
                                    </template>
                                </div>
                            </div>

                            <div class="timeline d-flex justify-content-between align-items-center mt-3">
                                <div class="text-center">
                                    <template x-if="!editing">
                                        <strong><?php echo e($segment['departure']['iataCode']); ?></strong>
                                    </template>
                                    <template x-if="editing">
                                        <input type="text" value="<?php echo e($segment['departure']['iataCode']); ?>" class="form-control">
                                    </template>

                                    <template x-if="!editing">
                                        <p class="mb-0 small text-muted"><?php echo e(\Carbon\Carbon::parse($segment['departure']['at'])->format('d/m/Y H:i')); ?></p>
                                    </template>
                                    <template x-if="editing">
                                        <input type="datetime-local" value="<?php echo e(\Carbon\Carbon::parse($segment['departure']['at'])->format('Y-m-d\TH:i')); ?>" class="form-control">
                                    </template>
                                </div>

                                <div class="timeline-line flex-grow-1 position-relative mx-3">
                                    <span class="text-success small position-absolute top-50 start-50 translate-middle">Direct</span>
                                    <div class="line bg-success"></div>
                                </div>

                                <div class="text-center">
                                    <template x-if="!editing">
                                        <strong><?php echo e($segment['arrival']['iataCode']); ?></strong>
                                    </template>
                                    <template x-if="editing">
                                        <input type="text" value="<?php echo e($segment['arrival']['iataCode']); ?>" class="form-control">
                                    </template>

                                    <template x-if="!editing">
                                        <p class="mb-0 small text-muted"><?php echo e(\Carbon\Carbon::parse($segment['arrival']['at'])->format('d/m/Y H:i')); ?></p>
                                    </template>
                                    <template x-if="editing">
                                        <input type="datetime-local" value="<?php echo e(\Carbon\Carbon::parse($segment['arrival']['at'])->format('Y-m-d\TH:i')); ?>" class="form-control">
                                    </template>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    Le <?php echo e($loop->iteration); ?><?php echo e($loop->iteration == 1 ? 'er' : 'e'); ?> vol aller n'existe pas.
                </div>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php else: ?>
        <div class="alert alert-warning">Aucun vol aller trouvé.</div>
    <?php endif; ?>

    
    <?php if(!empty($amadeusResults['return_flights'])): ?>
        <h3 class="text-primary mb-4">Vols Retour</h3>
        <?php $__currentLoopData = $amadeusResults['return_flights']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $returnGroup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $hasValidFlight = false;
                foreach ($returnGroup as $offer) {
                    if (isset($offer['itineraries'][0]['segments'][0])) {
                        $hasValidFlight = true;
                        break;
                    }
                }
            ?>

            <?php if($hasValidFlight): ?>
                <div class="flight-card border mb-4 p-4 rounded shadow-sm">
                    <?php $__currentLoopData = $returnGroup; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subIndex => $offer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if(isset($offer['itineraries'][0]['segments'][0])): ?>
                            <?php
                                $segment = $offer['itineraries'][0]['segments'][0];
                                $airline = \App\Models\Airline::where('iata_code', $segment['carrierCode'])->first();
                                $logoUrl = $airline
                                    ? 'https://logo.clearbit.com/'.$airline->domain.'?size=150'
                                    : 'https://via.placeholder.com/100?text='.$segment['carrierCode'];
                                $airlineName = $airline->name ?? $segment['carrierCode'];
                            ?>

                            <?php if($subIndex > 0): ?>
                                <div class="direct-arrow text-center my-4">
                                    <i class="fas fa-arrow-right text-success"></i>
                                    <span class="text-muted small">Direct</span>
                                </div>
                            <?php endif; ?>

                            <div class="d-flex align-items-center">
                                <img src="<?php echo e($logoUrl); ?>" alt="<?php echo e($airlineName); ?>" class="airline-logo me-3">
                                <div>
                                    <template x-if="!editing">
                                        <h5 class="mb-1 text-primary"><?php echo e($airlineName); ?></h5>
                                    </template>
                                    <template x-if="editing">
                                        <input type="text" value="<?php echo e($airlineName); ?>" class="form-control">
                                    </template>

                                    <template x-if="!editing">
                                        <small class="text-muted">Vol <?php echo e($segment['carrierCode']); ?> <?php echo e($segment['number']); ?></small>
                                    </template>
                                    <template x-if="editing">
                                        <input type="text" value="<?php echo e($segment['carrierCode']); ?> <?php echo e($segment['number']); ?>" class="form-control">
                                    </template>
                                </div>
                            </div>

                            <div class="timeline d-flex justify-content-between align-items-center mt-3">
                                <div class="text-center">
                                    <template x-if="!editing">
                                        <strong><?php echo e($segment['departure']['iataCode']); ?></strong>
                                    </template>
                                    <template x-if="editing">
                                        <input type="text" value="<?php echo e($segment['departure']['iataCode']); ?>" class="form-control">
                                    </template>

                                    <template x-if="!editing">
                                        <p class="mb-0 small text-muted"><?php echo e(\Carbon\Carbon::parse($segment['departure']['at'])->format('d/m/Y H:i')); ?></p>
                                    </template>
                                    <template x-if="editing">
                                        <input type="datetime-local" value="<?php echo e(\Carbon\Carbon::parse($segment['departure']['at'])->format('Y-m-d\TH:i')); ?>" class="form-control">
                                    </template>
                                </div>

                                <div class="timeline-line flex-grow-1 position-relative mx-3">
                                    <span class="text-success small position-absolute top-50 start-50 translate-middle">Direct</span>
                                    <div class="line bg-success"></div>
                                </div>

                                <div class="text-center">
                                    <template x-if="!editing">
                                        <strong><?php echo e($segment['arrival']['iataCode']); ?></strong>
                                    </template>
                                    <template x-if="editing">
                                        <input type="text" value="<?php echo e($segment['arrival']['iataCode']); ?>" class="form-control">
                                    </template>

                                    <template x-if="!editing">
                                        <p class="mb-0 small text-muted"><?php echo e(\Carbon\Carbon::parse($segment['arrival']['at'])->format('d/m/Y H:i')); ?></p>
                                    </template>
                                    <template x-if="editing">
                                        <input type="datetime-local" value="<?php echo e(\Carbon\Carbon::parse($segment['arrival']['at'])->format('Y-m-d\TH:i')); ?>" class="form-control">
                                    </template>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    Le <?php echo e($loop->iteration); ?><?php echo e($loop->iteration == 1 ? 'er' : 'e'); ?> vol retour n'existe pas.
                </div>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php else: ?>
        <div class="alert alert-warning">Aucun vol retour trouvé.</div>
    <?php endif; ?>
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
<?php /**PATH C:\angular\Vip_Travel_Project\resources\views/vendor/Flight/admin/offer/partials/amadeus_results_direct_multiple.blade.php ENDPATH**/ ?>