

<?php $__env->startSection('content'); ?>
<div class="container my-4">
    <h2 class="mb-4 text-primary fw-bold">
        <i class="fa fa-list"></i> Liste des offres
    </h2>

    <?php if(session('success')): ?>
        <div class="alert alert-success shadow-sm mb-3"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="alert alert-danger shadow-sm mb-3"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nom hôtel</th>
                            <th>Créée par</th>
                            <th>Statut</th>
                            <th>Date création</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $offres; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $offre): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($offre->id); ?></td>
                                <td><?php echo e($offre->hotel_scraping->hotel_name ?? '—'); ?></td>
                                <td><?php echo e($offre->creator->name ?? ''); ?> <?php echo e($offre->creator->prenom ?? ''); ?></td>
                                <td>
                                    <span class="badge 
                                        <?php if($offre->statut === 'valide'): ?> bg-success
                                        <?php elseif($offre->statut === 'refusee'): ?> bg-danger
                                        <?php elseif($offre->statut === 'brouillon'): ?> bg-warning text-dark
                                        <?php else: ?> bg-secondary
                                        <?php endif; ?>
                                    ">
                                        <?php echo e(ucfirst($offre->statut)); ?>

                                    </span>
                                </td>
                                <td><?php echo e($offre->created_at ? $offre->created_at->format('d/m/Y H:i') : '-'); ?></td>
                                <td class="text-end">
                                    <a href="<?php echo e(route('admin.offres.validation', $offre->id)); ?>" class="btn btn-primary btn-sm">
                                        <i class="fa fa-eye"></i> Voir
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">Aucune offre trouvée.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <?php echo e($offres->links('pagination::bootstrap-5')); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\angular\Vip_travel\Vip_Travel_Project\resources\views/admin/offres/index.blade.php ENDPATH**/ ?>