

<?php $__env->startSection('content'); ?>
<div class="container">

    <form action="<?php echo e(route('hotels.store.services')); ?>" method="POST">
        <?php echo csrf_field(); ?>

        <div id="services-wrapper">
            <div class="service-block border p-3 mb-3">
                <h5>Service</h5>
                <div class="mb-2">
                    <label>Type de service</label>
                    <select name="services[0][type_service]" class="form-control" required>
                        <option value="transfert offre">Transfert offre</option>
                        <option value="excursion offre">Excursion offre</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label>Date du service</label>
                    <input type="date" name="services[0][date_service]" class="form-control" required   min="<?php echo e(date('Y-m-d')); ?>" >
                </div>
                <div class="mb-2">
                    <label>Description</label>
                    <textarea name="services[0][description]" class="form-control" required></textarea>
                </div>
                <div class="mb-2">
                    <label>Prix</label>
                    <input type="number" name="services[0][prix]" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label>Capacité</label>
                    <input type="number" name="services[0][capacite]" class="form-control" required min="1">
                </div>
                <div class="mb-2">
                    <label>Type</label>
                    <select name="services[0][type]" class="form-control" required>
                        <option value="inclus">Inclus</option>
                        <option value="exclus">Exclus</option>
                    </select>
                </div>
            </div>
        </div>

        <button type="button" class="btn btn-secondary mb-3" onclick="addService()">Ajouter un autre service</button>

        <button type="submit" class="btn btn-primary">Enregistrer les services</button>
    </form>
</div>

<script>
    let serviceIndex = 1;

    function addService() {
        const wrapper = document.getElementById('services-wrapper');

        const block = document.createElement('div');
        block.classList.add('service-block', 'border', 'p-3', 'mb-3');
        block.innerHTML = `
            <h5>Service</h5>
            <div class="mb-2">
                <label>Type de service</label>
                <select name="services[${serviceIndex}][type_service]" class="form-control" required>
                    <option value="transfert offre">Transfert offre</option>
                    <option value="excursion offre">Excursion offre</option>
                </select>
            </div>
            <div class="mb-2">
                <label>Date du service</label>
                <input type="date" name="services[${serviceIndex}][date_service]" class="form-control" required min="${new Date().toISOString().split('T')[0]}">
            </div>
            <div class="mb-2">
                <label>Description</label>
                <textarea name="services[${serviceIndex}][description]" class="form-control" required></textarea>
            </div>
            <div class="mb-2">
                <label>Prix</label>
                <input type="number" name="services[${serviceIndex}][prix]" class="form-control" required>
            </div>
            <div class="mb-2">
                <label>Capacité</label>
                <input type="number" name="services[${serviceIndex}][capacite]" class="form-control" min="1" required>
            </div>
            <div class="mb-2">
                <label>Type</label>
                <select name="services[${serviceIndex}][type]" class="form-control" required>
                    <option value="inclus">Inclus</option>
                    <option value="exclus">Exclus</option>
                </select>
            </div>
        `;
        wrapper.appendChild(block);
        serviceIndex++;
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\angular\Vip_Travel_Project\resources\views/hotels/services.blade.php ENDPATH**/ ?>