<div>
    <div class="form-group">
        <label>Adresse</label>
        <input type="text" class="form-control" x-model="booking.address">
    </div>
    <div class="form-group">
        <label>Note Booking</label>
        <input type="text" class="form-control" x-model="booking.rating">
    </div>
    <div class="form-group">
        <label>Ajouter une image (URL)</label>
        <div class="input-group mb-2">
            <input type="text" class="form-control" x-model="newBookingImageUrl" placeholder="https://...">
            <button class="btn btn-primary" type="button"
                @click="if (newBookingImageUrl && !booking.images.includes(newBookingImageUrl)) { booking.images.push(newBookingImageUrl); newBookingImageUrl = ''; }">
                +
            </button>
        </div>
        <div class="row">
            <template x-for="(img, idx) in booking.images" :key="idx">
                <div class="col-md-3 mb-3 position-relative">
                    <img :src="img" class="img-fluid rounded border" style="max-height: 120px; object-fit: cover;">
                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0"
                        style="transform: translate(30%,-30%);"
                        @click="booking.images.splice(idx, 1)">✖</button>
                </div>
            </template>
            <template x-if="!booking.images || !booking.images.length">
                <div class="col-12 text-muted">Aucune image enregistrée.</div>
            </template>
        </div>
    </div>
    <button class="btn btn-success mt-2" @click="editBooking = false">Enregistrer</button>
</div>