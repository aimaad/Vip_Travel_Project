<form id="flight-offer-form" method="POST" action="<?php echo e(route('flight.admin.offers.store')); ?>">
    <?php echo csrf_field(); ?>
<input type="hidden" name="type" value="direct_single">
<input type="hidden" name="non_stop" value="true">
<input type="hidden" name="travel_class" value="ECONOMY">

<div class="card shadow-sm mb-4 border-0">
    <div class="card-header bg-primary text-white">
        <h4 class="mb-0">
            ✈️ <?php echo e(__("Recherche de Vol")); ?>

        </h4>
    </div>

    <div class="card-body">
        <!-- Infos locales -->
        <h5 class="mb-3 border-bottom pb-2">🗂️ Informations locales </h5>
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <label class="form-label">Nombre de places *</label>
                <input type="number" class="form-control" id="places" name="places" data-local-only required min="0">
            </div>
            <div class="col-md-3">
                <label class="form-label">Prix Adulte (MAD) *</label>
                <input type="number" class="form-control" id="price_adult" name="price_adult" data-local-only required min="0">
            </div>
            <div class="col-md-3">
                <label class="form-label">Prix Enfant (MAD) *</label>
                <input type="number" class="form-control" id="price_child" name="price_child"  data-local-only required min="0">
            </div>
            <div class="col-md-3">
                <label class="form-label">Prix Bébé (MAD) *</label>
                <input type="number" class="form-control" id="price_baby" name="price_baby" data-local-only min="0">
            </div>
        </div>

        <!-- Aller -->
        <h5 class="mb-3 border-bottom pb-2">🛫 <?php echo e(__("Aller (Outbound)")); ?></h5>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label"><?php echo e(__("Ville de départ (Code)")); ?> *</label>
                <input type="text" name="departure_city" class="form-control" required pattern="[A-Za-z]{3}" title="Code IATA (3 lettres)" maxlength="3">
            </div>
            <div class="col-md-6">
                <label class="form-label"><?php echo e(__("Ville d'arrivée (Code)")); ?> *</label>
                <input type="text" name="arrival_city" class="form-control" required pattern="[A-Za-z]{3}" title="Code IATA (3 lettres)" maxlength="3">
            </div>
            <div class="col-md-6">
                <label class="form-label"><?php echo e(__("Date de départ")); ?> *</label>
                <input type="date" name="departure_date" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label"><?php echo e(__("Numéro de vol")); ?> *</label>
                <input type="text" name="flight_number" class="form-control" required pattern="[A-Za-z]{2}[0-9]{1,4}" title="Ex: AF123">
            </div>
        </div>

        <!-- Retour -->
        <h5 class="mb-3 border-bottom pb-2">🛬 <?php echo e(__("Retour (Inbound)")); ?></h5>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label"><?php echo e(__("Ville de départ (Code)")); ?></label>
                <input type="text" name="return_departure_city" class="form-control" pattern="[A-Za-z]{3}" title="Code IATA (3 lettres)" maxlength="3">
            </div>
            <div class="col-md-6">
                <label class="form-label"><?php echo e(__("Ville d'arrivée (Code)")); ?></label>
                <input type="text" name="return_arrival_city" class="form-control" pattern="[A-Za-z]{3}" title="Code IATA (3 lettres)" maxlength="3">
            </div>
            <div class="col-md-6">
                <label class="form-label"><?php echo e(__("Date de retour")); ?></label>
                <input type="date" name="return_date" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label"><?php echo e(__("Numéro de vol retour")); ?></label>
                <input type="text" name="return_flight_number" class="form-control" pattern="[A-Za-z]{2}[0-9]{1,4}" title="Ex: AF456">
            </div>
        </div>

        <!-- Submit -->
        <div class="text-end">
            <button type="submit" class="btn btn-primary px-4">
                <span class="submit-text"><i class="bi bi-search me-1"></i> <?php echo e(__("Rechercher")); ?></span>
                <span class="spinner-border spinner-border-sm d-none" role="status"></span>
            </button>
        </div>
    </div>
</div>
</form>

<div id="amadeus-results-container" class="mt-4"></div>




<script>
    $(document).ready(function() {
    
        function setTodayAsMinDateForAllDateInputs() {
        const today = new Date().toISOString().split('T')[0];
        $('input[type="date"]').each(function () {
            $(this).attr('min', today);
        });
    }
    setTodayAsMinDateForAllDateInputs();
    $('input[name="departure_date"]').on('change', function () {
        const departureDate = $(this).val();
        const $returnDateInput = $('input[name="return_date"]');

        if (departureDate) {
            $returnDateInput.attr('min', departureDate);

            // Si return_date < departure_date, on la réinitialise
            if ($returnDateInput.val() && $returnDateInput.val() < departureDate) {
                $returnDateInput.val('');
            }
        }
    });

});
    </script><?php /**PATH C:\angular\Vip_Travel_Project\resources\views/vendor/Flight/admin/offer/forms/direct_single.blade.php ENDPATH**/ ?>