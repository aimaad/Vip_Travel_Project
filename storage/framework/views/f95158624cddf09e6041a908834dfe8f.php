

<?php $__env->startSection('content'); ?>
<div class="container my-4">
    <h2 class="mb-4 text-primary fw-bold">
        <i class="fa fa-check-circle"></i> Validation de l'offre
    </h2>

    <?php if(session('success')): ?>
        <div class="alert alert-success shadow-sm mb-3"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger shadow-sm mb-3"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <div class="card mb-4 shadow" style="border-radius: 1rem;">
        <div class="card-header bg-gradient bg-primary text-white d-flex justify-content-between align-items-center" style="border-radius: 1rem 1rem 0 0;">
            <div>
                <strong>
                    <i class="fa fa-building"></i>
                    Offre #<?php echo e($offre->id); ?> — <?php echo e($offre->hotel_scraping->hotel_name ?? ''); ?>

                </strong>
            </div>
            <span class="badge fs-6
                <?php if($offre->statut === 'valide'): ?> bg-success
                <?php elseif($offre->statut === 'refusee'): ?> bg-danger
                <?php else: ?> bg-warning text-dark <?php endif; ?>
            ">
                <?php echo e(ucfirst($offre->statut)); ?>

            </span>
        </div>
        <div class="card-body bg-light" style="border-radius: 0 0 1rem 1rem;">

            
            <?php if($offre->hotel_scraping): ?>
                <div class="mb-4 p-3 bg-white rounded shadow-sm border">
                    <h5 class="mb-3 text-secondary"><i class="fa fa-info-circle"></i> Informations scrappées de l'hôtel</h5>
                    <div class="row">
                        <div class="col-md-7">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <i class="fa fa-building"></i>
                                    <strong>Nom :</strong>
                                    <span class="text-primary"><?php echo e($offre->hotel_scraping->hotel_name); ?></span>
                                </li>
                                <li class="mb-2">
                                    <i class="fa fa-map-marker"></i>
                                    <strong>Adresse :</strong>
                                    <?php echo e($offre->hotel_scraping->address ?? 'Non disponible'); ?>

                                </li>
                                <li class="mb-2">
                                    <i class="fa fa-star text-warning"></i>
                                    <strong>Note :</strong>
                                    <?php echo e($offre->hotel_scraping->rating ?? 'Non disponible'); ?>

                                </li>
                            </ul>
                        </div>
                        <div class="col-md-5">
                            <?php
                                $images = $offre->hotel_scraping->images;
                                if (is_string($images)) {
                                    $images = json_decode($images, true);
                                }
                                $images = is_array($images) ? $images : [];
                            ?>
                            <?php if(count($images) > 0): ?>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php $__currentLoopData = $images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <a href="#" class="popup-img-link" data-img="<?php echo e($img); ?>">
                                            <img src="<?php echo e($img); ?>" alt="Image hôtel" class="rounded shadow" style="width: 80px; height: 65px; object-fit: cover; transition: transform .2s;">
                                        </a>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="mb-3">
                <strong><i class="fa fa-user-circle"></i> Créée par :</strong>
                <span class="text-secondary"><?php echo e($offre->creator->name ?? 'N/A'); ?> <?php echo e($offre->creator->prenom ?? ''); ?></span>
            </div>

            <hr>

            <h5 class="mt-4 mb-2"><i class="fa fa-door-open"></i> Détails des chambres</h5>
            <div class="bg-white rounded p-3 mb-4 shadow-sm border">
                <?php if(is_string($offre->room_types)): ?>
                    <?php $roomTypes = json_decode($offre->room_types, true); ?>
                <?php else: ?>
                    <?php $roomTypes = $offre->room_types; ?>
                <?php endif; ?>

                <?php if(is_array($roomTypes) && count($roomTypes)): ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Type</th>
                                    <th>Adultes</th>
                                    <th>Enfants</th>
                                    <th>Kids</th>
                                    <th>Bébés</th>
                                    <th>Prix</th>
                                    <th>Chambres dispo.</th>
                                    <th>Pension</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $roomTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($room['type'] ?? ''); ?></td>
                                        <td><?php echo e($room['adults'] ?? ''); ?></td>
                                        <td><?php echo e($room['children'] ?? ''); ?></td>
                                        <td><?php echo e($room['kids'] ?? ''); ?></td>
                                        <td><?php echo e($room['babies'] ?? ''); ?></td>
                                        <td><?php echo e($room['price'] ?? ''); ?></td>
                                        <td><?php echo e($room['available_rooms'] ?? ''); ?></td>
                                        <td><?php echo e($room['pension'] ?? ''); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <em>Aucune chambre renseignée.</em>
                <?php endif; ?>
            </div>

            <h5 class="mb-2"><i class="fa fa-concierge-bell"></i> Services</h5>
            <div class="bg-white rounded p-3 mb-4 shadow-sm border">
                <?php
                    $services = $offre->services;
                    if (is_string($services)) {
                        $services = json_decode($services, true);
                    } elseif (is_object($services) && method_exists($services, 'toArray')) {
                        $services = $services->toArray();
                    }
                    $services = is_array($services) ? $services : [];
                ?>
                <?php if(count($services)): ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Type</th>
                                    <th>Description</th>
                                    <th>Date</th>
                                    <th>Prix</th>
                                    <th>Capacité</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($service['type_service'] ?? $service['type'] ?? ''); ?></td>
                                        <td><?php echo e($service['description'] ?? ''); ?></td>
                                        <td><?php echo e($service['date_service'] ?? '-'); ?></td>
                                        <td>
                                            <span class="text-success"><?php echo e($service['prix'] ?? '-'); ?> €</span>
                                        </td>
                                        <td><?php echo e($service['capacite'] ?? '-'); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <em>Aucun service associé.</em>
                <?php endif; ?>
            </div>
       
            <h5 class="mb-3 text-primary"><i class="fa fa-plane-departure"></i> Détails des Vols</h5>

            <?php $__currentLoopData = $offre->offerFlights; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $flight): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-white p-4 rounded shadow-sm border mb-4">
                    <h6 class="text-dark fw-bold mb-3">
                        <i class="fa fa-plane"></i> 
                        <small class="text-muted">(type : <?php echo e(ucfirst($flight->flight_type)); ?>)</small>
                    </h6>
            
                    
                    <div class="row text-center mb-3">
                        <div class="col">
                            <div class="fw-bold text-secondary"><i class="fa fa-user text-primary"></i> Adulte</div>
                            <div class="text-success fs-5"><?php echo e(number_format($flight->price_adult, 2)); ?> €</div>
                        </div>
                        <div class="col">
                            <div class="fw-bold text-secondary"><i class="fa fa-child text-primary"></i> Enfant</div>
                            <div class="text-success fs-5"><?php echo e(number_format($flight->price_child, 2)); ?> €</div>
                        </div>
                        <div class="col">
                            <div class="fw-bold text-secondary"><i class="fa fa-baby text-primary"></i> Bébé</div>
                            <div class="text-success fs-5"><?php echo e(number_format($flight->price_baby, 2)); ?> €</div>
                        </div>
                        <div class="col">
                            <div class="fw-bold text-secondary"><i class="fa fa-chair text-primary"></i> Places dispo</div>
                            <div class="text-dark fs-5"><?php echo e($flight->places); ?></div>
                        </div>
                    </div>
            
                    
                    <?php $__currentLoopData = $flight->flightLegs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $leg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="border rounded p-3 mb-3">
                            <h6 class="text-secondary fw-bold">
                                <i class="fa fa-location-arrow"></i> <?php echo e(ucfirst($leg->direction)); ?>

                            </h6>
                            <ul class="list-unstyled mb-0">
                                <li><strong>Vol :</strong> <?php echo e($leg->flight_number); ?></li>
                                <li><strong>Compagnie :</strong>
                                    <?php if($leg->airline_logo): ?>
                                        <img src="<?php echo e($leg->airline_logo); ?>" style="height: 24px; vertical-align: middle;">
                                    <?php else: ?>
                                        <span class="text-muted">Non renseignée</span>
                                    <?php endif; ?>
                                </li>
                                <li><strong>Départ :</strong> <?php echo e($leg->departure_city); ?> — <?php echo e($leg->departure_date); ?> à <?php echo e($leg->departure_time); ?></li>
                                <li><strong>Arrivée :</strong> <?php echo e($leg->arrival_city); ?> — <?php echo e($leg->arrival_date); ?> à <?php echo e($leg->arrival_time); ?></li>
                            </ul>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            

            

            <div class="d-flex gap-3 mt-3">
                <?php if($offre->statut !== 'valide'): ?>
                    <form action="<?php echo e(route('admin.offres.valider', $offre->id)); ?>" method="post" class="d-inline">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-success shadow-sm px-4">
                            <i class="fa fa-check-circle"></i> Valider l'offre
                        </button>
                    </form>
                <?php endif; ?>

                <?php if($offre->statut !== 'refusee'): ?>
    <form action="<?php echo e(route('admin.offres.refuser', $offre->id)); ?>" method="post" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de refuser cette offre ?');">
        <?php echo csrf_field(); ?>
        <div class="mb-2">
            <textarea name="refus_commentaire" class="form-control" placeholder="Motif du refus (sera envoyé à l’utilisateur)" required rows="2"></textarea>
        </div>
        <button type="submit" class="btn btn-danger shadow-sm px-4 ms-2">
            <i class="fa fa-times-circle"></i> Refuser l'offre
        </button>
    </form>
<?php endif; ?>
            </div>
        </div>
    </div>
    <a href="<?php echo e(route('admin.offres.index')); ?>" class="btn btn-secondary shadow-sm">
        <i class="fa fa-arrow-left"></i> Retour à la liste
    </a>
</div>


<div id="popup-image-modal" class="modal fade" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content bg-transparent border-0">
      <div class="modal-body text-center p-0">
        <img src="" alt="Agrandissement" id="popup-image" class="img-fluid rounded shadow" style="max-height:80vh;">
      </div>
      <button type="button" class="btn-close position-absolute end-0 top-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
  </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.popup-img-link').forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            var imgSrc = this.getAttribute('data-img');
            var modalImg = document.getElementById('popup-image');
            modalImg.src = imgSrc;
            var modal = new bootstrap.Modal(document.getElementById('popup-image-modal'));
            modal.show();
        });
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\angular\Vip_travel\Vip_Travel_Project\resources\views/admin/offres/validation.blade.php ENDPATH**/ ?>