<form id="multi-offer-form" method="POST" action="{{ route('flight.admin.offers.store') }}">
    @csrf
    <input type="hidden" name="type" value="multi_flight_multiple">
    <input type="hidden" name="travel_class" value="ECONOMY">
    <input type="hidden" name="non_stop" value="true">

    <div class="card shadow-sm mb-4 border-0">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">
                ✈️ {{ __("Create Multiple Offers with Multiple Flights") }}
            </h4>
        </div>
        <div class="card-body">
            <!-- Offers Container -->
            <div id="offers-container">
                <!-- First Offer -->
                <div class="offer-item card mb-4" data-offer-index="0">
                    <div class="card-header bg-secondary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Offer</h5>
                            <button type="button" class="btn btn-sm btn-danger remove-offer-btn">Remove Offer</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- General Information -->
                        <h6 class="mb-3">🗂️ General Information</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label class="form-label">Nombre de places *</label>
                                <input type="number" class="form-control" name="offers[0][general][seats]" required min="0">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Prix Adulte (MAD) *</label>
                                <input type="number" class="form-control" name="offers[0][general][price_adult]" required min="0">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Prix Enfant (MAD) *</label>
                                <input type="number" class="form-control" name="offers[0][general][price_child]" required min="0" >
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Prix Bébé (MAD) *</label>
                                <input type="number" class="form-control" name="offers[0][general][price_baby]" required min="0">
                            </div>
                        </div>

                        <!-- Outbound Flights -->
                        <h6 class="mb-3">Outbound Flights</h6>
                        <div id="outbound-flights-container-0">
                            <!-- First Outbound Flight -->
                            <div class="flight-item card mb-3" data-flight-index="0">
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label class="form-label">Flight Number *</label>
                                            <input type="text" name="offers[0][outbound][0][flight_number]" class="form-control" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Departure Date *</label>
                                            <input type="date" name="offers[0][outbound][0][departure_date]" class="form-control" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Departure City *</label>
                                            <input type="text" name="offers[0][outbound][0][departure_city]" class="form-control" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Arrival City *</label>
                                            <input type="text" name="offers[0][outbound][0][arrival_city]" class="form-control" required>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-danger mt-3 remove-flight-btn">Remove Flight</button>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-primary add-flight-btn" data-offer-index="0" data-direction="outbound"  >Add Outbound Flight</button>

                        <!-- Return Flights -->
                        <h6 class="mt-4 mb-3">Return Flights</h6>
                        <div id="return-flights-container-0">
                            <!-- First Return Flight -->
                            <div class="flight-item card mb-3" data-flight-index="0">
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label class="form-label">Flight Number *</label>
                                            <input type="text" name="offers[0][return][0][flight_number]" class="form-control" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Departure Date *</label>
                                            <input type="date" name="offers[0][return][0][departure_date]" class="form-control" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Departure City *</label>
                                            <input type="text" name="offers[0][return][0][departure_city]" class="form-control" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Arrival City *</label>
                                            <input type="text" name="offers[0][return][0][arrival_city]" class="form-control" required>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-danger mt-3 remove-flight-btn">Remove Flight</button>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-primary add-flight-btn" data-offer-index="0" data-direction="return">Add Return Flight</button>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-outline-success add-offer-btn">Add Offer</button>
        </div>
    </div>

    <!-- Submit -->
    <div class="text-end">
        <button type="submit" class="btn btn-primary px-4">
            <span class="submit-text"><i class="bi bi-save me-1"></i> Save</span>
            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
        </button>
    </div>
</form>

<div id="amadeus-results-container" class="mt-4"></div>

<script>
$(document).ready(function () {
    setMinDateForAllDateInputs();
    let offerIndex = 1; // Commence à 1 car la première offre a l'index 0

    $('.add-offer-btn').on('click', function () {
        const container = $('#offers-container');
        const lastOffer = container.find('.offer-item').last();
        const newOffer = lastOffer.clone(true, true); // clone les événements aussi
        setMinDateForAllDateInputs();

        newOffer.attr('data-offer-index', offerIndex);

        // GENERAL INFO - copie les valeurs sauf departure_date
        newOffer.find('input, select, textarea').each(function () {
            const name = $(this).attr('name');
            if (name) {
                // Mise à jour des noms avec nouvel index
                const updatedName = name.replace(/\boffers\[\d+]/, `offers[${offerIndex}]`);
                $(this).attr('name', updatedName);

                // Vide uniquement les dates
                if (name.includes('departure_date')) {
                    $(this).val('');
                }
            }
        });

        // Flights - update IDs and names et vider les dates
        ['outbound', 'return'].forEach(direction => {
            const oldContainer = newOffer.find(`#${direction}-flights-container-${offerIndex - 1}`);
            oldContainer.attr('id', `${direction}-flights-container-${offerIndex}`);

            oldContainer.find('.flight-item').each(function (i) {
                $(this).attr('data-flight-index', i);
                $(this).find('input').each(function () {
                    const oldName = $(this).attr('name');
                    const updatedName = oldName
                        .replace(/\boffers\[\d+]/, `offers[${offerIndex}]`)
                        .replace(new RegExp(`\\[${direction}]\\[\\d+]`), `[${direction}][${i}]`);
                    $(this).attr('name', updatedName);

                    // Vider les dates
                    if ($(this).attr('type') === 'date') {
                        $(this).val('');
                    }
                });
            });
        });

        // Met à jour les data-offer-index sur les boutons "add flight"
        newOffer.find('.add-flight-btn').each(function () {
            $(this).attr('data-offer-index', offerIndex);
        });

        container.append(newOffer);

        // Gère les boutons "Remove Flight"
        toggleRemoveFlightButtons(newOffer.find(`#outbound-flights-container-${offerIndex}`));
        toggleRemoveFlightButtons(newOffer.find(`#return-flights-container-${offerIndex}`));

        offerIndex++;
    });

    // Supprimer une offre (si plusieurs)
    $(document).on('click', '.remove-offer-btn', function () {
        const container = $('#offers-container');
        if (container.find('.offer-item').length > 1) {
            $(this).closest('.offer-item').remove();
        }
    });

    // Ajouter vol aller/retour dynamiquement (adapté aux multiples offres)
    $(document).off('click', '.add-flight-btn').on('click', '.add-flight-btn', function () {
        setMinDateForAllDateInputs();
        const direction = $(this).data('direction'); // outbound ou return
        const offerIndex = $(this).data('offer-index');
        const container = $(`#${direction}-flights-container-${offerIndex}`);
        const lastFlight = container.find('.flight-item').last();
        const lastIndex = parseInt(lastFlight.attr('data-flight-index')) || 0;
        const newIndex = lastIndex + 1;

        const newFlight = lastFlight.clone();
        newFlight.attr('data-flight-index', newIndex);

        newFlight.find('input').each(function () {
            const oldName = $(this).attr('name');
            const updatedName = oldName
                .replace(new RegExp(`offers\\[\\d+\\]\\[${direction}]\\[\\d+]`), `offers[${offerIndex}][${direction}][${newIndex}]`);
            $(this).attr('name', updatedName).val('');
        });

        container.append(newFlight);
        toggleRemoveFlightButtons(container);
    });

    // Supprimer un vol
    $(document).on('click', '.remove-flight-btn', function () {
        const container = $(this).closest('.flight-item').parent();
        if (container.find('.flight-item').length > 1) {
            $(this).closest('.flight-item').remove();
            toggleRemoveFlightButtons(container);
        }
    });

    // Initial hide/show buttons for first offer flights
    toggleRemoveFlightButtons($('#outbound-flights-container-0'));
    toggleRemoveFlightButtons($('#return-flights-container-0'));
});

function toggleRemoveFlightButtons(container) {
    const flightItems = container.find('.flight-item');
    if (flightItems.length <= 1) {
        flightItems.find('.remove-flight-btn').hide();
    } else {
        flightItems.find('.remove-flight-btn').show();
    }
}
function setMinDateForAllDateInputs() {
    const today = new Date().toISOString().split('T')[0]; // 'YYYY-MM-DD'
    $('input[type="date"]').attr('min', today);
}
</script>
