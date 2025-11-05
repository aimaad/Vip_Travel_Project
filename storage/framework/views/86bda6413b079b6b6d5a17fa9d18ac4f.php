

<?php $__env->startSection('content'); ?>
<style>
    .table-modern th, .table-modern td { vertical-align: middle !important; }
    .table-modern tbody tr { transition: box-shadow 0.2s, transform 0.2s; }
    .table-modern tbody tr:hover { box-shadow: 0 2px 12px rgba(60, 60, 150, 0.11); transform: scale(1.01); background: #f9faff; }
    .actions-group .btn { margin-right: 4px; margin-bottom: 2px; }
    @media (max-width: 576px) {
        .table-responsive { font-size: 0.95rem; }
        .actions-group .btn { width: 100%; margin-bottom: 6px;}
    }
</style>
<div class="container py-4">
    <h2 class="mb-4"><i class="bi bi-list-task"></i> Mes Offres</h2>
    <?php if($offres->isEmpty()): ?>
        <div class="alert alert-warning shadow-sm">Aucune offre trouvée.</div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table table-modern align-middle table-hover shadow-sm rounded">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Nom hôtel</th>
                    <th>Statut</th>
                    <th>Date création</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $offres; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $offre): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><span class="fw-bold"><?php echo e($offre->id); ?></span></td>
                        <td><?php echo e(optional(\App\Models\HotelScraping::find($offre->hotel_scraping_id))->hotel_name ?? '-'); ?></td>
                        <td>
                            <?php switch($offre->statut):
                                case ('brouillon'): ?>
                                    <span class="badge bg-secondary"><i class="bi bi-pencil-square"></i> Brouillon</span>
                                    <?php break; ?>
                                <?php case ('valide'): ?>
                                    <span class="badge bg-success"><i class="bi bi-check-circle"></i> Validée</span>
                                    <?php break; ?>
                                <?php case ('refusee'): ?>
                                    <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Refusée</span>
                                    <?php break; ?>
                                <?php case ('archivee'): ?>
                                    <span class="badge bg-dark"><i class="bi bi-archive"></i> Archivée</span>
                                    <?php break; ?>
                                <?php case ('arretee'): ?>
                                    <span class="badge bg-warning text-dark"><i class="bi bi-stop-circle"></i> Arrêtée</span>
                                    <?php break; ?>
                                <?php default: ?>
                                    <span class="badge bg-light text-dark"><?php echo e($offre->statut ?? '-'); ?></span>
                            <?php endswitch; ?>
                        </td>
                        <td><span class="text-muted"><?php echo e(\Carbon\Carbon::parse($offre->created_at)->format('d/m/Y H:i')); ?></span></td>
                        <td>
                            <div class="actions-group d-flex flex-wrap justify-content-center">
                            <?php if($offre->statut === 'brouillon'): ?>
                                <a href="<?php echo e(route('offre.duplicate', $offre->id)); ?>" class="btn btn-outline-secondary btn-sm" title="Dupliquer">
                                    <i class="bi bi-files"></i>
                                </a>
                                <a href="<?php echo e(route('offre.edit', $offre->id)); ?>" class="btn btn-outline-primary btn-sm" title="Modifier">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="<?php echo e(route('offre.archive', $offre->id)); ?>" method="POST" style="display:inline;">
                                    <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                    <button class="btn btn-outline-dark btn-sm" type="submit" title="Archiver" onclick="return confirm('Archiver cette offre ?')">
                                        <i class="bi bi-archive"></i>
                                    </button>
                                </form>
                            <?php elseif($offre->statut === 'valide'): ?>
                                <a href="<?php echo e(route('offre.duplicate', $offre->id)); ?>" class="btn btn-outline-secondary btn-sm" title="Dupliquer">
                                    <i class="bi bi-files"></i>
                                </a>
                                <form action="<?php echo e(route('offre.stop', $offre->id)); ?>" method="POST" style="display:inline;">
                                    <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                    <button class="btn btn-outline-warning btn-sm" type="submit" title="Arrêter" onclick="return confirm('Arrêter cette offre ?')">
                                        <i class="bi bi-stop-circle"></i>
                                    </button>
                                </form>
                                <form action="<?php echo e(route('offre.archive', $offre->id)); ?>" method="POST" style="display:inline;">
                                    <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                    <button class="btn btn-outline-dark btn-sm" type="submit" title="Archiver" onclick="return confirm('Archiver cette offre ?')">
                                        <i class="bi bi-archive"></i>
                                    </button>
                                </form>
                                <a href="<?php echo e(route('offre.detail', $offre->id)); ?>" class="btn btn-outline-success btn-sm" title="Voir détail">
                                    <i class="bi bi-eye"></i>
                                </a>
                            <?php elseif($offre->statut === 'refusee'): ?>
                                <a href="<?php echo e(route('offre.refus.detail', $offre->id)); ?>" class="btn btn-outline-danger btn-sm" title="Voir le refus">
                                    <i class="bi bi-eye-slash"></i>
                                </a>
                            <?php else: ?>
                                <a href="<?php echo e(route('offre.detail', $offre->id)); ?>" class="btn btn-outline-info btn-sm" title="Voir détail">
                                    <i class="bi bi-eye"></i>
                                </a>
                            <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
        <div class="d-flex justify-content-center">
            <?php echo e($offres->links('pagination::bootstrap-5')); ?>

        </div>
    <?php endif; ?>
</div>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\angular\Vip_travel\Vip_Travel_Project\resources\views/hotels/mes_offres.blade.php ENDPATH**/ ?>