
<?php $__env->startSection('content'); ?>
<div class="container py-4" style="padding-top: 105px !important;">
    <h2 class="mb-4">Nos offres</h2>
    <div class="row">
        <?php $__currentLoopData = $offres; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $offre): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $scraping = $offre->hotel_scraping; ?>
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm h-100">
                    <?php if(!empty($scraping->images[0])): ?>
                        <img src="<?php echo e($scraping->images[0]); ?>" class="card-img-top" style="height:180px;object-fit:cover;">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo e($scraping->hotel_name ?? 'Offre'); ?></h5>
                        <p class="card-text"><?php echo e($scraping->address ?? ''); ?></p>
                        <a href="<?php echo e(route('offres.show', $offre->id)); ?>" class="btn btn-primary">Voir l’offre</a>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <div class="mt-4 d-flex justify-content-center">
        <?php echo e($offres->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\angular\Vip_Travel_Project\resources\views/offres/index.blade.php ENDPATH**/ ?>