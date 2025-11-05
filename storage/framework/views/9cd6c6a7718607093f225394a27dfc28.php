

<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Compagnies Aériennes</h3>
            <a href="<?php echo e(route('admin.airlines.create')); ?>" class="btn btn-primary">
                <i class="icon ion-ios-add"></i> Ajouter une compagnie
            </a>
        </div>
    </div>
    <div class="card-body">
        <?php if(session('success')): ?>
            <div class="alert alert-success"><?php echo e(session('success')); ?></div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Logo</th>
                        <th>Code IATA</th>
                        <th>Nom</th>
                        <th>Domaine</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $airlines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $airline): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="text-center">
                            <img src="<?php echo e($airline->logo_url); ?>" 
                                 alt="<?php echo e($airline->name); ?>"
                                 class="img-thumbnail"
                                 style="max-height: 50px;">
                        </td>
                        <td><?php echo e($airline->iata_code); ?></td>
                        <td><?php echo e($airline->name); ?></td>
                        <td><?php echo e($airline->domain); ?></td>
                        <td>
                            <form action="<?php echo e(route('admin.airlines.destroy', $airline->id)); ?>" method="POST" style="display: inline-block;" 
                                onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette compagnie ?');">
                              <?php echo csrf_field(); ?>
                              <?php echo method_field('DELETE'); ?>
                              <button type="submit" class="btn btn-sm btn-danger">
                                <i class="icon ion-ios-trash"></i>                              </button>
                          </form>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make("Layout::admin.app", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\angular\Vip_travel\Vip_Travel_Project\resources\views/admin/airlines/index.blade.php ENDPATH**/ ?>