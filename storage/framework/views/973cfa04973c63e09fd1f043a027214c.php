

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="alert alert-success">
        <h4>Votre offre a été validée !</h4>
        <p>
            L'offre pour l'hôtel <strong><?php echo e($hotelScraping->hotel_name ?? '-'); ?></strong> a été <strong>validée</strong> par l'administrateur.
        </p>
    </div>

    <div class="card mb-4">
        <div class="card-header">Détails de l'offre validée</div>
        <div class="card-body">
            <p><strong>Nom de l'hôtel :</strong> <?php echo e($hotelScraping->hotel_name ?? '-'); ?></p>
            <p><strong>Adresse :</strong> <?php echo e($hotelScraping->address ?? '-'); ?></p>
            <p><strong>Nombre total de chambres :</strong> <?php echo e($offre->total_rooms ?? '-'); ?></p>
            <p><strong>Statut :</strong> <?php echo e(ucfirst($offre->statut)); ?></p>
            <p><strong>Date de validation :</strong> <?php echo e($offre->updated_at ? \Carbon\Carbon::parse($offre->updated_at)->format('d/m/Y H:i') : '-'); ?></p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Types de chambres</div>
        <div class="card-body">
            <?php if(!empty($types)): ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Adultes</th>
                            <th>Enfants</th>
                            <th>Kids</th>
                            <th>Bébés</th>
                            <th>Chambres dispo</th>
                            <th>Pension</th>
                            <th>Prix (€)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($room['type'] ?? '-'); ?></td>
                                <td><?php echo e($room['adults'] ?? '-'); ?></td>
                                <td><?php echo e($room['children'] ?? '-'); ?></td>
                                <td><?php echo e($room['kids'] ?? '-'); ?></td>
                                <td><?php echo e($room['babies'] ?? '-'); ?></td>
                                <td><?php echo e($room['available_rooms'] ?? '-'); ?></td>
                                <td><?php echo e($room['pension'] ?? '-'); ?></td>
                                <td><?php echo e($room['price'] ?? '-'); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-warning">Aucun type de chambre renseigné.</div>
            <?php endif; ?>
        </div>
    </div>
    <div class="card mb-4">
        <div class="card-header">Données scrappées Booking</div>
        <div class="card-body">
            <p><strong>Adresse :</strong> <?php echo e($scraped['address'] ?? '-'); ?></p>
            <p><strong>Note Booking :</strong> <?php echo e($scraped['rating'] ?? '-'); ?></p>
            <h5>Images :</h5>
            <div class="row">
                <?php if(isset($scraped['images']) && is_array($scraped['images']) && count($scraped['images'])): ?>
                    <?php $__currentLoopData = $scraped['images']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-3 mb-3">
                            <img src="<?php echo e($img); ?>" alt="image" class="img-fluid rounded border" style="max-height: 200px; object-fit: cover;">
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <div class="col-12">Aucune image trouvée.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Services associés</div>
        <div class="card-body">
            <?php if(!empty($services)): ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Type de service</th>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Prix</th>
                            <th>Capacité</th>
                            <th>Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($service->type_service ?? '-'); ?></td>
                                <td><?php echo e($service->date_service ?? '-'); ?></td>
                                <td><?php echo e($service->description ?? '-'); ?></td>
                                <td><?php echo e($service->prix ?? '-'); ?> €</td>
                                <td><?php echo e($service->capacite ?? '-'); ?></td>
                                <td><?php echo e($service->type ?? '-'); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-warning">Aucun service associé.</div>
            <?php endif; ?>
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
    

    <a href="<?php echo e(route('mes.offres')); ?>" class="btn btn-secondary">
        Retour à toutes mes offres
    </a>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\angular\Vip_travel\Vip_Travel_Project\resources\views/hotels/detail_validee.blade.php ENDPATH**/ ?>