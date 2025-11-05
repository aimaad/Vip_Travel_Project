<form id="flight-offer-form" method="POST">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="type" value="direct_multiple">
    <input type="hidden" name="non_stop" value="true">
    <input type="hidden" name="travel_class" value="ECONOMY">

    <div class="card shadow-sm mb-4 border-0">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">
                ✈️ <?php echo e(__("Recherche de Vol Multiple")); ?>

            </h4>
        </div>

        <div class="card-body">
            <!-- Infos locales -->
            <h5 class="mb-3 border-bottom pb-2">🗂️ Informations locales </h5>
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label">Nombre de places</label>
                    <input type="number" class="form-control" name="places" data-local-only required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Prix Adulte (MAD)</label>
                    <input type="number" class="form-control" name="price_adult" data-local-only required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Prix Enfant (MAD)</label>
                    <input type="number" class="form-control" name="price_child" data-local-only required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Prix Bébé (MAD)</label>
                    <input type="number" class="form-control" name="price_baby" data-local-only required>
                </div>
            </div>

            <!-- Vols multiples -->
            <h5 class="mb-3 border-bottom pb-2">🛫 <?php echo e(__("Vols")); ?></h5>
            <div id="flights-container">
                <!-- Premier vol -->
                <div class="flight-item card mb-3" data-index="0">
                    <div class="card-body">
                        <!-- Ligne 1 : Numéro de vol et Date de départ -->
                        <div class="row g-3 align-items-center mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Numéro de vol *</label>
                                <input type="text" name="flights[0][flight_number]" class="form-control" required pattern="[A-Za-z]{2}[0-9]{1,4}" title="Ex: AF123">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date de départ *</label>
                                <input type="date" name="flights[0][departure_date]" class="form-control" required>
                            </div>
                        </div>
                
                        <!-- Ligne 2 : Ville de départ et Ville d'arrivée -->
                        <div class="row g-3 align-items-center">
                            <div class="col-md-6">
                                <label class="form-label">Ville de départ (Code) *</label>
                                <input type="text" name="flights[0][departure_city]" class="form-control" required pattern="[A-Za-z]{3}" title="Code IATA (3 lettres)" maxlength="3">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Ville d'arrivée (Code) *</label>
                                <input type="text" name="flights[0][arrival_city]" class="form-control" required pattern="[A-Za-z]{3}" title="Code IATA (3 lettres)" maxlength="3">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bouton pour ajouter un nouveau vol -->
            <div class="mb-4">
                <button type="button" id="add-flight-btn" class="btn btn-outline-primary">
                    <i class="bi bi-plus-circle"></i> Ajouter un autre vol
                </button>
            </div>


             <!-- -------------------------- vol retour -------------------------------------->


            <h5 class="mb-3 border-bottom pb-2">🛬 <?php echo e(__("Vols Retour")); ?></h5>
            <div id="return-flights-container">
                <!-- Premier vol retour -->
                <div class="flight-item card mb-3" data-index="0">
                    <div class="card-body">
                        <!-- Ligne 1 : Numéro de vol et Date de départ -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Numéro de vol *</label>
                                <input type="text" name="return_flights[0][flight_number]" class="form-control" required pattern="[A-Za-z]{2}[0-9]{1,4}" title="Ex: AF456">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date de départ *</label>
                                <input type="date" name="return_flights[0][departure_date]" class="form-control" required>
                            </div>
                        </div>
            
                        <!-- Ligne 2 : Ville de départ et Ville d'arrivée -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Ville de départ *</label>
                                <input type="text" name="return_flights[0][departure_city]" class="form-control" required pattern="[A-Za-z]{3}" title="Code IATA (3 lettres)" maxlength="3">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Ville d'arrivée *</label>
                                <input type="text" name="return_flights[0][arrival_city]" class="form-control" required pattern="[A-Za-z]{3}" title="Code IATA (3 lettres)" maxlength="3">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button type="button" id="add-return-flight-btn" class="btn btn-outline-primary mb-4">Ajouter un autre vol retour</button>



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

    // Formatage des codes IATA
    $('input[name*="[departure_city]"], input[name*="[arrival_city]"]').on('input', function() {
        this.value = this.value.toUpperCase().replace(/[^A-Z]/g, '');
    });

    // Formatage des numéros de vol
    $('input[name*="[flight_number]"]').on('input', function() {
        this.value = this.value.toUpperCase();
    });

    // Compteurs pour les nouveaux vols et vols retour
    let flightCounter = 1;
    let returnFlightCounter = 1;

    // Ajouter un nouveau vol
$('#add-flight-btn').click(function() {
    const newFlightHtml = `
    <div class="flight-item card mb-3" data-index="${flightCounter}">
        <div class="card-body">
            <!-- Ligne 1 : Numéro de vol et Date de départ -->
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Numéro de vol *</label>
                    <input type="text" name="flights[${flightCounter}][flight_number]" class="form-control" required pattern="[A-Za-z]{2}[0-9]{1,4}" title="Ex: AF123">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Date de départ *</label>
                    <input type="date" name="flights[${flightCounter}][departure_date]" class="form-control" required>
                </div>
            </div>
            <!-- Ligne 2 : Ville de départ et Ville d'arrivée -->
            <div class="row g-3 mt-3">
                <div class="col-md-6">
                    <label class="form-label">Ville de départ (Code) *</label>
                    <input type="text" name="flights[${flightCounter}][departure_city]" class="form-control" required pattern="[A-Za-z]{3}" title="Code IATA (3 lettres)" maxlength="3">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Ville d'arrivée (Code) *</label>
                    <input type="text" name="flights[${flightCounter}][arrival_city]" class="form-control" required pattern="[A-Za-z]{3}" title="Code IATA (3 lettres)" maxlength="3">
                </div>
            </div>
            <div class="text-end mt-3">
                <button type="button" class="btn btn-sm btn-outline-danger remove-flight-btn">
                    <i class="bi bi-trash"></i> Supprimer
                </button>
            </div>
        </div>
    </div>`;
    
    $('#flights-container').append(newFlightHtml);
    flightCounter++;
    setTodayAsMinDateForAllDateInputs();
});
    // Ajouter un nouveau vol retour
$('#add-return-flight-btn').click(function() {
    const newReturnFlightHtml = `
    <div class="flight-item card mb-3" data-index="${returnFlightCounter}">
        <div class="card-body">
            <!-- Ligne 1 : Numéro de vol et Date de départ -->
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Numéro de vol *</label>
                    <input type="text" name="return_flights[${returnFlightCounter}][flight_number]" class="form-control" required pattern="[A-Za-z]{2}[0-9]{1,4}" title="Ex: AF456">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Date de départ *</label>
                    <input type="date" name="return_flights[${returnFlightCounter}][departure_date]" class="form-control" required>
                </div>
            </div>
            <!-- Ligne 2 : Ville de départ et Ville d'arrivée -->
            <div class="row g-3 mt-3">
                <div class="col-md-6">
                    <label class="form-label">Ville de départ *</label>
                    <input type="text" name="return_flights[${returnFlightCounter}][departure_city]" class="form-control" required pattern="[A-Za-z]{3}" title="Code IATA (3 lettres)" maxlength="3">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Ville d'arrivée *</label>
                    <input type="text" name="return_flights[${returnFlightCounter}][arrival_city]" class="form-control" required pattern="[A-Za-z]{3}" title="Code IATA (3 lettres)" maxlength="3">
                </div>
            </div>
            <div class="text-end mt-3">
                <button type="button" class="btn btn-sm btn-outline-danger remove-flight-btn">
                    <i class="bi bi-trash"></i> Supprimer
                </button>
            </div>
        </div>
    </div>`;
    
    $('#return-flights-container').append(newReturnFlightHtml);
    returnFlightCounter++;
    setTodayAsMinDateForAllDateInputs();
});

    // Supprimer un vol ou vol retour
    $(document).on('click', '.remove-flight-btn', function() {
        $(this).closest('.flight-item').remove();
    });

    // Soumission du formulaire
    $('#flight-offer-form').on('submit', function(e) {
        e.preventDefault();

        const form = $(this);
        const button = form.find('button[type="submit"]');
        const submitText = button.find('.submit-text');
        const spinner = button.find('.spinner-border');

        submitText.addClass('d-none');
        spinner.removeClass('d-none');
        button.prop('disabled', true);
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();

        const formData = new FormData(this);

        $.ajax({
            url: "<?php echo e(route('flight.admin.offers.store')); ?>",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
    if (response.status === 'processing') {
        // Redirection immédiate sans attendre les résultats
       
        window.location.href = "<?php echo e(route('hotels.create')); ?>";
    }
    else if (response.errors) {
        $.each(response.errors, function (field, messages) {
            const input = form.find('[name*="' + field.replace(/\./g, '][') + '"]').first();
            input.addClass('is-invalid');
            input.after('<div class="invalid-feedback">' + messages.join('<br>') + '</div>');
        });
    }
    else {
        $('#amadeus-results-container').html(
            '<div class="alert alert-danger">' + (response.message || 'Unknown error') + '</div>'
        );
    }
},

            error: function(xhr) {
                let errorMsg = 'An error occurred. Please try again.';
                try {
                    const response = JSON.parse(xhr.responseText);
                    errorMsg = response.message || errorMsg;
                    if (response.errors) {
                        errorMsg += '<br>' + JSON.stringify(response.errors);
                    }
                } catch (e) {
                    errorMsg = xhr.responseText || errorMsg;
                }
                $('#amadeus-results-container').html(
                    '<div class="alert alert-danger">' + errorMsg + '</div>'
                );
            },
            complete: function() {
                submitText.removeClass('d-none');
                spinner.addClass('d-none');
                button.prop('disabled', false);
            }
        });
    });
});

</script><?php /**PATH C:\angular\Vip_travel\Vip_Travel_Project\resources\views/vendor/Flight/admin/offer/forms/direct_multiple.blade.php ENDPATH**/ ?>