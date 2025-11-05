<form id="flight-offer-form" method="POST"  action="<?php echo e(route('flight.admin.offers.store')); ?>" >
    <?php echo csrf_field(); ?>
    <input type="hidden" name="type" value="multi_flight_single">
    <input type="hidden" name="non_stop" value="true">
    <input type="hidden" name="travel_class" value="ECONOMY">

    <div class="card shadow-sm mb-4 border-0">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">✈️ <?php echo e(__("Recherche de Vols Multiples Aller-Retour")); ?></h4>
        </div>

        <div class="card-body">
            <!-- Offres -->
            <h5 class="mb-3 border-bottom pb-2">🛫 <?php echo e(__("Offres Aller-Retour")); ?></h5>
            <div id="offers-container">
                <div class="offer card border-secondary mb-3">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0">Offre 1</h6>
                    </div>
                    <div class="card-body">
                        <!-- Infos générales pour l'offre -->
                        <div class="mb-3">
                            <h6 class="text-info">Informations Générales</h6>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Nombre de places *</label>
                                    <input type="number" name="offers[0][places]" class="form-control place_number" required min="0">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Prix Adulte (MAD) *</label>
                                    <input type="number" name="offers[0][price_adult]" class="form-control adult_price " required min="0">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Prix Enfant (MAD) *</label>
                                    <input type="number" name="offers[0][price_child]" class="form-control enfant_price " required min="0">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Prix Bébé (MAD) *</label>
                                    <input type="number" name="offers[0][price_baby]" class="form-control bebe_price" required min="0">
                                </div>
                            </div>
                        </div>

                        <!-- Aller -->
                        <div class="mb-3">
                            <h6 class="text-primary">Aller</h6>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Numéro de vol *</label>
                                    <input type="text" name="offers[0][outbound][flight_number]" class="form-control flight_number_aller" required pattern="[A-Za-z]{2}[0-9]{1,4}" title="Ex: AF123" >
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Ville de départ *</label>
                                    <input type="text" name="offers[0][outbound][departure_city]" class="form-control departure-city" required pattern="[A-Za-z]{3}" title="Code IATA (3 lettres)" maxlength="3">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Ville d'arrivée *</label>
                                    <input type="text" name="offers[0][outbound][arrival_city]" class="form-control arrival-city" required pattern="[A-Za-z]{3}" title="Code IATA (3 lettres)" maxlength="3">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Date de départ *</label>
                                    <input type="date" name="offers[0][outbound][departure_date]" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <!-- Retour -->
                        <div>
                            <h6 class="text-success">Retour</h6>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Numéro de vol *</label>
                                    <input type="text" name="offers[0][return][flight_number]" class="form-control flight_number_retour" required pattern="[A-Za-z]{2}[0-9]{1,4}" title="Ex: AF456">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Ville de départ *</label>
                                    <input type="text" name="offers[0][return][departure_city]" class="form-control departure-city" required pattern="[A-Za-z]{3}" title="Code IATA (3 lettres)" maxlength="3">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Ville d'arrivée *</label>
                                    <input type="text" name="offers[0][return][arrival_city]" class="form-control arrival-city" required pattern="[A-Za-z]{3}" title="Code IATA (3 lettres)" maxlength="3">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Date de retour *</label>
                                    <input type="date" name="offers[0][return][return_date]" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-3">
                            <button type="button" class="btn btn-sm btn-danger remove-offer">
                                <?php echo e(__("Supprimer cette offre")); ?>

                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-secondary mb-3" id="add-offer">
                + <?php echo e(__("Ajouter une offre")); ?>

            </button>
        </div>

        <!-- Submit -->
        <div class="card-footer text-end">
            <button type="submit" class="btn btn-primary px-4">
                <?php echo e(__("Rechercher")); ?>

            </button>
        </div>
    </div>
</form>

<div id="amadeus-results-container" class="mt-4"></div>

<script>
    function updateReturnMinDate(context) {
    const departureInput = context.find('input[name*="[outbound][departure_date]"]');
    const returnInput = context.find('input[name*="[return][return_date]"]');

    departureInput.on('change', function () {
        const departureDate = $(this).val();
        returnInput.attr('min', departureDate);
    });
}
    $(document).ready(function () {





function setTodayAsMinDateForAllDateInputs() {
    const today = new Date().toISOString().split('T')[0];
    $('input[type="date"]').each(function () {
        $(this).attr('min', today);
    });
}

// 🟢 Appelle-la ici pour fixer la date minimale au chargement
setTodayAsMinDateForAllDateInputs();

        
        updateReturnMinDate($('.offer').first());

        let offerCount = 1;
    
        // Ajouter une offre
        $('#add-offer').on('click', function () {
            const lastOffer = $('#offers-container .offer').last();
            const lastDepartureCity = lastOffer.find('.departure-city').first().val();
            const lastArrivalCity = lastOffer.find('.arrival-city').first().val();
            const lastPlaceNumber = lastOffer.find('.place_number').first().val();
            const lastAdultPrice = lastOffer.find('.adult_price').first().val();
            const lastEnfantPrice = lastOffer.find('.enfant_price').first().val();
            const lastBebePrice = lastOffer.find('.bebe_price').first().val();
            const lastFlightNumberAller = lastOffer.find('.flight_number_aller').first().val();
            const lastFlightNumberRetour = lastOffer.find('.flight_number_retour').first().val();
        


    
            const newOffer = `
                <div class="offer card border-secondary mb-3">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0">Offre ${offerCount + 1}</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h6 class="text-info">Informations Générales</h6>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Nombre de places *</label>
                                    <input type="number" name="offers[${offerCount}][places]" class="form-control place_number" required value="${lastPlaceNumber}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Prix Adulte (MAD) *</label>
                                    <input type="number" name="offers[${offerCount}][price_adult]" class="form-control adult_price" required   value="${lastAdultPrice}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Prix Enfant (MAD) *</label>
                                    <input type="number" name="offers[${offerCount}][price_child]" class="form-control enfant_price" required  value="${lastEnfantPrice}" >
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Prix Bébé (MAD) *</label>
                                    <input type="number" name="offers[${offerCount}][price_baby]" class="form-control bebe_price" required  value="${lastBebePrice}" >
                                </div>
                            </div>
                        </div>
    
                        <div class="mb-3">
                            <h6 class="text-primary">Aller</h6>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Numéro de vol *</label>
                                    <input type="text" name="offers[${offerCount}][outbound][flight_number]" class="form-control flight_number_aller" required pattern="[A-Za-z]{2}[0-9]{1,4}" title="Ex: AF123" value="${lastFlightNumberAller}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Ville de départ *</label>
                                    <input type="text" name="offers[${offerCount}][outbound][departure_city]" class="form-control departure-city" value="${lastDepartureCity}" required pattern="[A-Za-z]{3}" title="Code IATA (3 lettres)" maxlength="3">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Ville d'arrivée *</label>
                                    <input type="text" name="offers[${offerCount}][outbound][arrival_city]" class="form-control arrival-city" value="${lastArrivalCity}" required pattern="[A-Za-z]{3}" title="Code IATA (3 lettres)" maxlength="3">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Date de départ *</label>
                                    <input type="date" name="offers[${offerCount}][outbound][departure_date]" class="form-control" required>
                                </div>
                            </div>
                        </div>
    
                        <div>
                            <h6 class="text-success">Retour</h6>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Numéro de vol *</label>
                                    <input type="text" name="offers[${offerCount}][return][flight_number]" class="form-control flight_number_retour " required pattern="[A-Za-z]{2}[0-9]{1,4}" title="Ex: AF456"  value="${lastFlightNumberRetour}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Ville de départ *</label>
                                    <input type="text" name="offers[${offerCount}][return][departure_city]" class="form-control departure-city" value="${lastArrivalCity}" required pattern="[A-Za-z]{3}" title="Code IATA (3 lettres)" maxlength="3">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Ville d'arrivée *</label>
                                    <input type="text" name="offers[${offerCount}][return][arrival_city]" class="form-control arrival-city" value="${lastDepartureCity}" required pattern="[A-Za-z]{3}" title="Code IATA (3 lettres)" maxlength="3">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Date de retour *</label>
                                    <input type="date" name="offers[${offerCount}][return][return_date]" class="form-control" required>
                                </div>
                            </div>
                        </div>
    
                        <div class="text-end mt-3">
                            <button type="button" class="btn btn-sm btn-danger remove-offer">
                                <?php echo e(__("Supprimer cette offre")); ?>

                            </button>
                        </div>
                    </div>
                </div>
            `;
            $('#offers-container').append(newOffer);
            updateReturnMinDate($('#offers-container .offer').last());
            offerCount++;
            setTodayAsMinDateForAllDateInputs();

        });
    
        // Supprimer une offre
        $(document).on('click', '.remove-offer', function () {
            $(this).closest('.offer').remove();
            offerCount--;
        });
    
        // Handle Form Submission via AJAX
        $('#flight-offer-form').on('submitt', function (e) {
            e.preventDefault();
    
            const form = $(this);
            const button = form.find('button[type="submit"]');
            const submitText = button.find('.submit-text');
            const spinner = button.find('.spinner-border');
    
            submitText.addClass('d-none'); // Hide submit text
            spinner.removeClass('d-none'); // Show spinner
            button.prop('disabled', true); // Disable button
    
            // Prepare form data
            const formData = form.serialize();
    
            // AJAX POST Request
            $.ajax({
                url: "<?php echo e(route('flight.admin.offers.store')); ?>", // Update the route name if needed
                type: 'POST',
                data: formData,
                success: function (response) {
                    if (response.status === 'success') {
                        $('#amadeus-results-container').html(response.results); // Display results
                    } 
                    else if (response.status === 'processing') {
        // ✅ Intègre ici le polling
        $('#amadeus-results-container').html('<div class="alert alert-info">🔍 Recherche en cours...</div>');
        pollResults(response.uuid);
    }   
    else if (response.errors) {
                        // Highlight errors
                        $.each(response.errors, function (field, messages) {
                            const input = form.find('[name="' + field + '"]');
                            input.addClass('is-invalid');
                            input.after('<div class="invalid-feedback">' + messages.join('<br>') + '</div>');
                        });
                    } else {
                        $('#amadeus-results-container').html(
                            '<div class="alert alert-danger">' + (response.message || 'Unknown error') + '</div>'
                        );
                    }
                },
                error: function (xhr) {
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
                complete: function () {
                    submitText.removeClass('d-none'); // Show submit text
                    spinner.addClass('d-none'); // Hide spinner
                    button.prop('disabled', false); // Enable button
                }
            });
        });
    });

    function updateReturnCities(container) {
    const depInput = container.find('input[name$="[outbound][departure_city]"]');
    const arrInput = container.find('input[name$="[outbound][arrival_city]"]');
    const returnDepInput = container.find('input[name$="[return][departure_city]"]');
    const returnArrInput = container.find('input[name$="[return][arrival_city]"]');

    function syncCities() {
        const depVal = depInput.val().toUpperCase();
        const arrVal = arrInput.val().toUpperCase();

        if (depVal.length === 3 && arrVal.length === 3) {
            returnDepInput.val(arrVal);
            returnArrInput.val(depVal);
        }
    }

    depInput.on('input', syncCities);
    arrInput.on('input', syncCities);
}

// Initialiser pour la première offre
updateReturnCities($('.offer').first());

// Initialiser pour les nouvelles offres ajoutées
$('#add-offer').on('click', function () {
    setTimeout(() => {
        const newOffer = $('.offer').last();
        updateReturnCities(newOffer);
    }, 100); // attendre que l'élément soit bien inséré
});



function pollResults(uuid) {
    const interval = setInterval(function () {
        $.get(`/flight-search/status/${uuid}`, function (data) {
            if (data.status === 'done') {
                clearInterval(interval);
                $('#amadeus-results-container').html(data.results_html);
            } else if (data.status === 'error') {
                clearInterval(interval);
                $('#amadeus-results-container').html(
                    '<div class="alert alert-danger">❌ Erreur : ' + data.error + '</div>'
                );
            }
        });
    }, 3000); // toutes les 3 secondes
}

    </script><?php /**PATH C:\angular\Vip_travel\Vip_Travel_Project\resources\views/vendor/Flight/admin/offer/forms/multi_flight_single.blade.php ENDPATH**/ ?>