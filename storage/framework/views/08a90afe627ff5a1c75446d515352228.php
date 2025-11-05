
<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="alert alert-danger">
        <h4>Offre refusée par l'administrateur</h4>
        <p>
            Votre offre pour l'hôtel <strong><?php echo e($hotelScraping->hotel_name ?? '-'); ?></strong> a été refusée.<br>
            <strong>Motif du refus :</strong>
            <br>
            <span class="text-warning"><?php echo e($offre->refus_commentaire ?? 'Aucun motif précisé.'); ?></span>
        </p>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            Détails de l'offre
        </div>
        <div class="card-body">
            <p><strong>Nom de l'hôtel :</strong> <?php echo e($hotelScraping->hotel_name ?? '-'); ?></p>
            <p><strong>Adresse :</strong> <?php echo e($hotelScraping->address ?? '-'); ?></p>
            <p><strong>Nombre total de chambres :</strong> <?php echo e($offre->total_rooms ?? '-'); ?></p>
            <?php
                $types = is_array($offre->room_types) 
                    ? $offre->room_types 
                    : (json_decode($offre->room_types, true) ?? []);
            ?>
            
            <p><strong>Types de chambres :</strong> 
                <?php echo e(implode(', ', array_map(function($room) {
                    return is_array($room) && isset($room['type']) ? $room['type'] : $room;
                }, $types)) ?: '-'); ?>

            </p>
            <p><strong>Statut :</strong> <?php echo e(ucfirst($offre->statut)); ?></p>
            <p><strong>Date de création :</strong> <?php echo e($offre->created_at ? \Carbon\Carbon::parse($offre->created_at)->format('d/m/Y H:i') : '-'); ?></p>
        </div>
    </div>
    <div class="card mb-4">
        <div class="card-header">Détails des Vols</div>
        <div class="card-body">
            <?php if(count($offerFlights)): ?>
                <?php $__currentLoopData = $offerFlights; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $flight): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="mb-4 p-3 border rounded">
                        <h6 class="text-primary fw-bold">
                            Type : <?php echo e(ucfirst($flight->flight_type)); ?>

                        </h6>
    
                        <div class="row mb-3">
                            <div class="col"><strong>Places :</strong> <?php echo e($flight->places); ?></div>
                            <div class="col"><strong>Adulte :</strong> <?php echo e($flight->price_adult); ?> €</div>
                            <div class="col"><strong>Enfant :</strong> <?php echo e($flight->price_child); ?> €</div>
                            <div class="col"><strong>Bébé :</strong> <?php echo e($flight->price_baby); ?> €</div>
                        </div>
    
                        <?php $__currentLoopData = $flight->flightLegs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $leg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="p-3 mb-2 border bg-light rounded">
                                <h6 class="text-secondary">Trajet : <?php echo e(ucfirst($leg->direction)); ?></h6>
                                <ul class="mb-0 list-unstyled">
                                    <li><strong>Numéro de vol :</strong> <?php echo e($leg->flight_number); ?></li>
                                    <li><strong>Départ :</strong> <?php echo e($leg->departure_city); ?> le <?php echo e($leg->departure_date); ?> à <?php echo e($leg->departure_time); ?></li>
                                    <li><strong>Arrivée :</strong> <?php echo e($leg->arrival_city); ?> le <?php echo e($leg->arrival_date); ?> à <?php echo e($leg->arrival_time); ?></li>
                                    <li><strong>Compagnie :</strong>
                                        <?php if($leg->airline_logo): ?>
                                            <img src="<?php echo e($leg->airline_logo); ?>" alt="logo" style="height: 20px;">
                                        <?php else: ?>
                                            <em>Non fournie</em>
                                        <?php endif; ?>
                                    </li>
                                </ul>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
                <div class="alert alert-warning">Aucun vol associé à cette offre.</div>
            <?php endif; ?>
        </div>
    </div>
    
 <!-- <a href="<?php echo e(route('hotels.confirm', ['offre' => $offre->id])); ?>" class="btn btn-primary">
        Modifier et soumettre à nouveau
    </a>
    <a href="<?php echo e(route('mes.offres')); ?>" class="btn btn-secondary">
        Retour à mes offres
    </a>
-->
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\angular\Vip_Travel_Project\resources\views/hotels/detail_refus.blade.php ENDPATH**/ ?>