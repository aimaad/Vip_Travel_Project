

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <h2>Test Résultat Recherche Vol #<?php echo e($result->id); ?></h2>
    <div class="mb-3">
        <strong>Status :</strong> <?php echo e($result->status); ?><br>
        <strong>Places :</strong> <?php echo e($result->places); ?><br>
        <strong>Prix Adulte :</strong> <?php echo e($result->price_adult); ?><br>
        <strong>Prix Enfant :</strong> <?php echo e($result->price_child); ?><br>
        <strong>Prix Bébé :</strong> <?php echo e($result->price_baby); ?><br>
    </div>
    <hr>
    <div>
        <?php echo $result->results_html; ?>

    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\angular\Vip_travel\Vip_Travel_Project\resources\views\vendor/flight/test_result.blade.php ENDPATH**/ ?>