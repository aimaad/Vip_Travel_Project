

<?php $__env->startSection('content'); ?>
<?php if(session('success')): ?>
    <div class="alert alert-success">
        <?php echo e(session('success')); ?>

       
    </div>
<?php endif; ?>
<?php if(session('warning')): ?>
    <div class="alert alert-warning">
        <?php echo e(session('warning')); ?>

       
    </div>
<?php endif; ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><?php echo e(__("Create New Flight Offer")); ?></h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card card-primary card-outline">
                                <div class="card-body box-profile">
                                    <h4 class="text-center"><?php echo e(__("Offer Type")); ?></h4>
                                    <ul class="list-group list-group-unbordered mb-3" id="offer-type-list">
                                        <?php $__currentLoopData = ['direct_single', 'direct_multiple', 'multi_flight_single', 'multi_flight_multiple']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li class="list-group-item p-0">
                                            <button type="button" 
                                                    data-type="<?php echo e($type); ?>" 
                                                    class="offer-type-link w-100 text-left btn btn-link <?php if(request('type') === $type): ?> active <?php endif; ?>">
                                                <b><?php echo e(__(ucfirst(str_replace('_', ' ', $type)))); ?></b>
                                                <p class="text-muted mb-0">
                                                    <?php if($type === 'direct_single'): ?>
                                                        <?php echo e(__("One offer with one direct flight")); ?>

                                                    <?php elseif($type === 'direct_multiple'): ?>
                                                        <?php echo e(__("Multiple offers with direct flights")); ?>

                                                    <?php elseif($type === 'multi_flight_single'): ?>
                                                        <?php echo e(__("One offer with multiple flights")); ?>

                                                    <?php else: ?>
                                                        <?php echo e(__("Multiple offers with multiple flights")); ?>

                                                    <?php endif; ?>
                                                </p>
                                            </button>
                                        </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div id="offer-form-container">
                                <?php if(request()->has('type')): ?>
                                    <div class="text-center p-4">
                                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                                        <p><?php echo e(__("Loading form...")); ?></p>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-info">
                                        <?php echo e(__("Please select an offer type from the left menu to continue")); ?>

                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .offer-type-link {
        cursor: pointer;
        display: block;
        padding: 10px;
        color: #333;
        text-decoration: none;
        background: none;
        border: none;
        text-align: left;
    }
    .offer-type-link:hover {
        background-color: #f8f9fa;
    }
    .offer-type-link.active {
        background-color: #e9ecef;
        font-weight: bold;
    }
    #offer-form-container {
        min-height: 300px;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    // Debug initial
  

    // Fonction améliorée pour charger le formulaire
    function loadOfferForm(type) {
        
        // Mise à jour visuelle
        $('.offer-type-link').removeClass('active');
        $(`.offer-type-link[data-type="${type}"]`).addClass('active');
        
        // Mise à jour URL
        const newUrl = `<?php echo e(route('flight.admin.offers.create')); ?>?type=${type}`;
        history.pushState(null, '', newUrl);
        
        // Affichage loader
        $('#offer-form-container').html(`
            <div class="text-center p-4">
                <i class="fas fa-spinner fa-spin fa-2x"></i>
                <p><?php echo e(__("Loading form...")); ?></p>
            </div>
        `);

        // Requête AJAX avec gestion d'erreur améliorée
        $.ajax({
            url: "<?php echo e(route('flight.admin.offers.get_form')); ?>",
            type: 'POST',
            data: {
                type: type,
                _token: "<?php echo e(csrf_token()); ?>"
            },
            dataType: 'html', // Important pour la réponse HTML
            success: function(response) {
                $('#offer-form-container').html(response);
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                let errorMsg = "<?php echo e(__('Error loading form. Please try again.')); ?>";
                
                if (xhr.status === 404) {
                    errorMsg = "<?php echo e(__('Form not found. Contact support.')); ?>";
                }
                
                $('#offer-form-container').html(`
                    <div class="alert alert-danger">
                        ${errorMsg}
                        <br><small>Error: ${xhr.status} - ${error}</small>
                        <button onclick="window.location.reload()" class="btn btn-sm btn-default mt-2">
                            <?php echo e(__('Reload Page')); ?>

                        </button>
                    </div>
                `);
            }
        });
    }

    // Chargement initial
    <?php if(request()->has('type')): ?>
        loadOfferForm("<?php echo e(request('type')); ?>");
    <?php endif; ?>

    // Gestion des clics
    $(document).on('click', '.offer-type-link', function(e) {
        e.preventDefault();
        const type = $(this).data('type');
        loadOfferForm(type);
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\angular\Vip_travel\Vip_Travel_Project\resources\views/vendor/Flight/admin/offer/create.blade.php ENDPATH**/ ?>