

<?php $__env->startSection('content'); ?>
<div class="container my-4" x-data="confirmationPage()">
    <h2 class="mb-4">Confirmation de l'hébergement</h2>
    <?php if($errors->any()): ?>
    <div class="alert alert-danger">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div><?php echo e($message); ?></div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php endif; ?>

    
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <strong>Données saisies – Chambres d'hôtel</strong>
            <button type="button" class="btn btn-light btn-sm" @click="editHotel = !editHotel">
                <span x-show="!editHotel">✏️ Modifier</span>
                <span x-show="editHotel">❌ Annuler</span>
            </button>
        </div>
        <div class="card-body">
            <template x-if="!editHotel">
                <div>
                    <p><strong>Nom de l'hôtel :</strong> <span x-text="hotel.name"></span></p>
                    <p><strong>Nombre total de chambres :</strong> <span x-text="hotel.total_rooms"></span></p>
                </div>
            </template>
            <template x-if="editHotel">
                <div>
                    <div class="form-group">
                        <label>Nom de l'hôtel</label>
                        <input type="text" class="form-control" x-model="hotel.name">
                    </div>
                    <div class="form-group">
                        <label>Nombre total de chambres</label>
                        <input type="number" class="form-control" x-model="hotel.total_rooms" min="1">
                    </div>
                    <button class="btn btn-success mt-2" @click="editHotel = false">Enregistrer</button>
                </div>
            </template>

            <h5 class="mt-3">Types de chambres :</h5>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="thead-light">
                        <tr>
                            <th>Type</th>
                            <th>Adultes</th>
                            <th>Enfants</th>
                            <th>Kids</th>
                            <th>Bébés</th>
                            <th>Chambres disponibles</th>
                            <th>Pension</th>
                            <th>Prix (€)</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(room, i) in hotel.room_types" :key="i">
                            <tr>
                                <td style="min-width: 150px;">
                                    <template x-if="editRoom === i">
                                        <select class="form-control" x-model="room.type">
                                            <option value="single">Single</option>
                                            <option value="double">Double</option>
                                            <option value="triple">Triple</option>
                                        </select>
                                    </template>
                                    <template x-if="editRoom !== i">
                                        <span x-text="room.type.charAt(0).toUpperCase() + room.type.slice(1)"></span>
                                    </template>
                                </td>
                                <td>
                                    <template x-if="editRoom === i">
                                        <input type="number" class="form-control" x-model="room.adults" min="1">
                                    </template>
                                    <template x-if="editRoom !== i">
                                        <span x-text="room.adults"></span>
                                    </template>
                                </td>
                                <td>
                                    <template x-if="editRoom === i">
                                        <input type="number" class="form-control" x-model="room.children" min="0">
                                    </template>
                                    <template x-if="editRoom !== i">
                                        <span x-text="room.children"></span>
                                    </template>
                                </td>
                                <td>
                                    <template x-if="editRoom === i">
                                        <input type="number" class="form-control" x-model="room.kids" min="0">
                                    </template>
                                    <template x-if="editRoom !== i">
                                        <span x-text="room.kids"></span>
                                    </template>
                                </td>
                                <td>
                                    <template x-if="editRoom === i">
                                        <input type="number" class="form-control" x-model="room.babies" min="0">
                                    </template>
                                    <template x-if="editRoom !== i">
                                        <span x-text="room.babies"></span>
                                    </template>
                                </td>
                                <td>
                                    <template x-if="editRoom === i">
                                        <input type="number" class="form-control" x-model="room.available_rooms" min="1">
                                    </template>
                                    <template x-if="editRoom !== i">
                                        <span x-text="room.available_rooms"></span>
                                    </template>
                                </td>
                                <td style="min-width: 160px;">
                                    <template x-if="editRoom === i">
                                        <select class="form-control" x-model="room.pension">
                                            <option value="RO">RO (Room Only)</option>
                                            <option value="PDJ">PDJ (Petit Déjeuner)</option>
                                            <option value="DP">DP (Demi Pension)</option>
                                            <option value="PC">PC (Pension Complète)</option>
                                        </select>
                                    </template>
                                    <template x-if="editRoom !== i">
                                        <span x-text="{
                                            'RO': 'RO (Room Only)',
                                            'PDJ': 'PDJ (Petit Déjeuner)',
                                            'DP': 'DP (Demi Pension)',
                                            'PC': 'PC (Pension Complète)'
                                        }[room.pension] || room.pension"></span>
                                    </template>
                                </td>
                                <td>
                                    <template x-if="editRoom === i">
                                        <input type="number" step="0.01" class="form-control" x-model="room.price" min="0">
                                    </template>
                                    <template x-if="editRoom !== i">
                                        <span x-text="room.price"></span>
                                    </template>
                                </td>
                                <td>
                                    <button class="btn btn-danger btn-sm" @click="removeRoom(i)">🗑️</button>
                                    <template x-if="editRoom === i">
                                        <button class="btn btn-success btn-sm mb-1 ms-1" @click="editRoom = null">✔️</button>
                                    </template>
                                    <template x-if="editRoom !== i">
                                        <button class="btn btn-light btn-sm mb-1 ms-1" @click="editRoom = i">✏️</button>
                                    </template>
                                </td>
                            </tr>
                        </template>
                        <template x-if="!hotel.room_types || !hotel.room_types.length">
                            <tr>
                                <td colspan="9" class="text-center">Aucun type de chambre saisi.</td>
                            </tr>
                        </template>
                    </tbody>
                </table>
                <button class="btn btn-outline-primary btn-sm" @click="addRoomType()">+ Ajouter un type de chambre</button>
            </div>
        </div>
    </div>

   
   <?php
   $scrapingInProgress =
       (empty($scraped) || empty($scraped['address']) || $scraped['address'] === 'Adresse non trouvée')
       && (empty($scraped['rating']) || $scraped['rating'] === 'Note non trouvée')
       && (empty($scraped['images']) || (is_array($scraped['images']) && count($scraped['images']) === 0));
   $scrapingError =
       (!empty($scraped) && (
           empty($scraped['address']) || $scraped['address'] === 'Adresse non trouvée' ||
           empty($scraped['rating']) || $scraped['rating'] === 'Note non trouvée' ||
           empty($scraped['images']) || (is_array($scraped['images']) && count($scraped['images']) === 0)
       ));
?>
<div class="card mb-4 shadow-sm">
   <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
       <strong>Données récupérées depuis Booking</strong>
       <button type="button" class="btn btn-light btn-sm"
           @click="editBooking = !editBooking"
           :disabled="<?php echo e($scrapingInProgress ? 'true' : 'false'); ?>">
           <span x-show="!editBooking">✏️ Modifier</span>
           <span x-show="editBooking">❌ Annuler</span>
       </button>
   </div>
   <div class="card-body">
       <?php if($scrapingInProgress): ?>
           <div class="alert alert-warning">
               Les informations Booking sont en cours de récupération…<br>
               Merci de patienter quelques instants.
           </div>
           <script>
               setTimeout(function() {
                   window.location.reload();
               }, 5000);
           </script>
       <?php else: ?>
           <?php if($scrapingError): ?>
               <div class="alert alert-danger">
                   Une erreur est survenue lors de la récupération automatique des données Booking.<br>
                   <strong>Veuillez compléter manuellement les informations de l'hôtel ci-dessous.</strong>
               </div>
               <div x-show="true">
                   <?php echo $__env->make('hotels._booking_form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
               </div>
           <?php else: ?>
               <template x-if="!editBooking">
                   <div>
                       <p><strong>Adresse :</strong> <span x-text="booking.address"></span></p>
                       <p><strong>Note Booking :</strong> <span x-text="booking.rating"></span></p>
                       <h5 class="mt-3">Images :</h5>
                       <div class="row">
                           <template x-for="(img, idx) in booking.images" :key="idx">
                               <div class="col-md-3 mb-3">
                                   <img :src="img" alt="image" class="img-fluid rounded border" style="max-height: 200px; object-fit: cover;">
                               </div>
                           </template>
                           <template x-if="!booking.images || !booking.images.length">
                               <div class="col-12">Aucune image trouvée.</div>
                           </template>
                       </div>
                   </div>
               </template>
               <template x-if="editBooking">
                   <?php echo $__env->make('hotels._booking_form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
               </template>
           <?php endif; ?>
       <?php endif; ?>
   </div>
</div>
    
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
            <strong>Données saisies – Services</strong>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="thead-light">
                        <tr>
                            <th>Type de service</th>
                            <th>Date du service</th>
                            <th>Description</th>
                            <th>Prix</th>
                            <th>Capacité</th>
                            <th>Type</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(service, i) in services" :key="i">
                            <tr>
                                <td style="min-width: 160px;">
                                    <template x-if="editService === i">
                                        <select class="form-control" x-model="service.type_service">
                                            <option value="transfert offre">Transfert offre</option>
                                            <option value="excursion offre">Excursion offre</option>
                                        </select>
                                    </template>
                                    <template x-if="editService !== i">
                                        <span x-text="service.type_service"></span>
                                    </template>
                                </td>
                                <td>
                                    <template x-if="editService === i">
                                        <input type="date" class="form-control" x-model="service.date_service" :min="today" />
                                    </template>
                                    <template x-if="editService !== i">
                                        <span x-text="formatDate(service.date_service)"></span>
                                    </template>
                                </td>
                                <td>
                                    <template x-if="editService === i">
                                        <input type="text" class="form-control" x-model="service.description">
                                    </template>
                                    <template x-if="editService !== i">
                                        <span x-text="service.description"></span>
                                    </template>
                                </td>
                                <td>
                                    <template x-if="editService === i">
                                        <input type="number" class="form-control" x-model="service.prix" min="0" step="0.01">
                                    </template>
                                    <template x-if="editService !== i">
                                        <span x-text="formatPrix(service.prix)"></span>
                                    </template>
                                </td>
                                <td>
                                    <template x-if="editService === i">
                                        <input type="number" class="form-control" x-model="service.capacite" min="1">
                                    </template>
                                    <template x-if="editService !== i">
                                        <span x-text="service.capacite"></span>
                                    </template>
                                </td>
                                <td style="min-width: 120px;">
                                    <template x-if="editService === i">
                                        <select class="form-control" x-model="service.type">
                                            <option value="inclus">Inclus</option>
                                            <option value="exclus">Exclus</option>
                                        </select>
                                    </template>
                                    <template x-if="editService !== i">
                                        <span x-text="service.type.charAt(0).toUpperCase() + service.type.slice(1)"></span>
                                    </template>
                                </td>
                                <td>
                                    <button class="btn btn-danger btn-sm" @click="removeService(i)">🗑️</button>
                                    <template x-if="editService === i">
                                        <button class="btn btn-success btn-sm mb-1 ms-1"
                                           
                                            @click="if(isServiceComplete(service)){ editService = null }"
                                        >✔️</button>
                                    </template>
                                    <template x-if="editService !== i">
                                        <button class="btn btn-light btn-sm mb-1 ms-1" @click="editService = i">✏️</button>
                                    </template>
                                </td>
                            </tr>
                        </template>
                        <template x-if="!services || !services.length">
                            <tr>
                                <td colspan="7" class="text-center">Aucun service saisi.</td>
                            </tr>
                        </template>
                    </tbody>
                </table>
                <button class="btn btn-outline-primary btn-sm" @click="addService()">+ Ajouter un service</button>
            </div>
        </div>
    </div>

    <?php if($flightResultsHtml): ?>
    <?php if($flightType === 'direct_single'): ?>
        <?php echo $__env->make('vendor.Flight.admin.offer.partials.amadeus_results_direct_single', [
            'amadeusResults' => $flightResultsHtml,
            'request' => $request ?? []
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php elseif($flightType === 'direct_multiple'): ?>
        <?php echo $__env->make('vendor.Flight.admin.offer.partials.amadeus_results_direct_multiple', [
            'amadeusResults' => $flightResultsHtml['data'][0]['data'] ?? [],
            'request' => $request ?? []
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
         <?php elseif($flightType === 'multi_flight_single'): ?>
         <?php echo $__env->make('vendor.Flight.admin.offer.partials.amadeus_results_multi_flight_single', [
             'amadeusResults' => $flightResultsHtml,
             'request' => $request ?? []
         ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
          <?php elseif($flightType === 'multi_flight_multiple'): ?>
          <?php echo $__env->make('vendor.Flight.admin.offer.partials.amadeus_results_multi_flight_multiple', [
              'amadeusResults' => $flightResultsHtml,
              'request' => $request ?? []
          ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php else: ?>
        <div class="alert alert-warning">
            Type de vol inconnu ou non pris en charge : <strong><?php echo e($flightType); ?></strong>
        </div>
    <?php endif; ?>
<?php else: ?>
    <div class="alert alert-warning">Aucun résultat de vol.</div>
<?php endif; ?>

<?php if(!empty($flightSearch) && $flightType === 'multi_flight_single'): ?>
    <?php $__currentLoopData = $flightSearch; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $offer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light">
                <strong>Offre <?php echo e($index + 1); ?></strong>
            </div>
            <div class="card-body">
                <ul class="mb-0 list-unstyled">
                    <li><strong>Nombre de places :</strong> <?php echo e($offer['places'] ?? '-'); ?></li>
                    <li><strong>Prix adulte :</strong> <?php echo e($offer['price_adult'] ?? '-'); ?> MAD</li>
                    <li><strong>Prix enfant :</strong> <?php echo e($offer['price_child'] ?? '-'); ?> MAD</li>
                    <li><strong>Prix bébé :</strong> <?php echo e($offer['price_baby'] ?? '-'); ?> MAD</li>
                </ul>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php elseif(!empty($flightSearch) && $flightType === 'multi_flight_multiple'): ?>
    <?php $__currentLoopData = $flightSearch; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $offer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light">
                <strong>Offre <?php echo e($index + 1); ?></strong>
            </div>
            <div class="card-body">
                <ul class="mb-0 list-unstyled">
                    <li><strong>Nombre de places :</strong> <?php echo e($offer['general']['seats'] ?? '-'); ?></li>
                    <li><strong>Prix adulte :</strong> <?php echo e($offer['general']['price_adult'] ?? '-'); ?> MAD</li>
                    <li><strong>Prix enfant :</strong> <?php echo e($offer['general']['price_child'] ?? '-'); ?> MAD</li>
                    <li><strong>Prix bébé :</strong> <?php echo e($offer['general']['price_baby'] ?? '-'); ?> MAD</li>
                </ul>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php elseif(!empty($flightUser)): ?>
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <ul class="mb-0 list-unstyled">
                <li><strong>Nombre de places :</strong> <?php echo e($flightUser['places'] ?? '-'); ?></li>
                <li><strong>Prix adulte :</strong> <?php echo e($flightUser['price_adult'] ?? '-'); ?> MAD</li>
                <li><strong>Prix enfant :</strong> <?php echo e($flightUser['price_child'] ?? '-'); ?> MAD</li>
                <li><strong>Prix bébé :</strong> <?php echo e($flightUser['price_baby'] ?? '-'); ?> MAD</li>
            </ul>
        </div>
    </div>
<?php endif; ?>




    
    
    <?php if(isset($editOffreId)): ?>
        <form action="<?php echo e(route('offre.updateConfirmation', $editOffreId)); ?>" method="POST" class="mt-3">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PATCH'); ?>
            <input type="hidden" name="input" :value="JSON.stringify(hotel)">
            <input type="hidden" name="booking" :value="JSON.stringify(booking)">
            <input type="hidden" name="services" :value="JSON.stringify(services)">
            <button type="submit" class="btn btn-success btn-lg w-100">
                Mettre à jour l'offre
            </button>
        </form>
    <?php else: ?>
        <form action="<?php echo e(route('hotels.confirm.store')); ?>" method="POST" class="mt-3">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="input" :value="JSON.stringify(hotel)">
            <input type="hidden" name="booking" :value="JSON.stringify(booking)">
            <input type="hidden" name="services" :value="JSON.stringify(services)">
            <button type="submit" class="btn btn-success btn-lg w-100">
                Confirmer
            </button>
        </form>
    <?php endif; ?>
</div>


<script src="//unpkg.com/alpinejs" defer></script>
<script>
function confirmationPage() {
    return {
        hotel: (() => {
            let h = <?php echo json_encode($input, 15, 512) ?>;
            let arr = [];
            if (h.room_types) {
                if (Array.isArray(h.room_types)) {
                    arr = h.room_types;
                } else if (typeof h.room_types === 'object') {
                    arr = Object.values(h.room_types);
                }
            }
            arr = arr.map(room => ({
                ...room,
                adults: Number(room.adults),
                children: Number(room.children),
                kids: Number(room.kids),
                babies: Number(room.babies),
                available_rooms: Number(room.available_rooms),
                price: Number(room.price)
            }));
            h.room_types = arr;
            return h;
        })(),
        services: <?php echo json_encode($services, 15, 512) ?>,
        booking: (() => {
            let b = <?php echo json_encode($scraped, 15, 512) ?>;
            return {
                address: b.address || '',
                rating: b.rating || '',
                images: Array.isArray(b.images) ? b.images : [],
                get imagesString() { return this.images.join('\n'); },
                set imagesString(val) { this.images = val.split('\n').filter(Boolean); }
            };
        })(),
        editBooking: false,
        scrapingInProgress() {
            return (
                (!this.booking.address || this.booking.address === 'Adresse non trouvée') &&
                (!this.booking.rating || this.booking.rating === 'Note non trouvée') &&
                (!this.booking.images || !this.booking.images.length)
            );
        },
        scrapingError() {
            return (
                !this.booking.address || this.booking.address === 'Adresse non trouvée' ||
                !this.booking.rating || this.booking.rating === 'Note non trouvée' ||
                !this.booking.images || !this.booking.images.length
            );
        },
        newBookingImageUrl: '',
        scrapingStatus: '<?php echo e($scrapingStatus); ?>',
        editHotel: false,
        editRoom: null,
        editService: null,
        editServices: false,
        addRoomType() {
            if (!Array.isArray(this.hotel.room_types)) {
                this.hotel.room_types = [];
            }
            this.hotel.room_types.push({
                type: 'single', adults: 1, children: 0, kids: 0, babies: 0,
                available_rooms: 1, pension: 'RO (Room Only)', price: 0
            });
            this.editRoom = this.hotel.room_types.length - 1;
        },
        removeRoom(index) {
            this.hotel.room_types.splice(index, 1);
            if (this.editRoom === index) this.editRoom = null;
        },
        addService() {
            if (!this.services) this.services = [];
            if (this.services.length > 0) {
                const last = this.services[this.services.length - 1];
                if (!this.isServiceComplete(last)) {
                    alert('Veuillez remplir tous les champs du service avant d\'en ajouter un nouveau.');
                    this.editService = this.services.length - 1;
                    return;
                }
            }
            this.services.push({
                type_service: 'transfert offre',
                date_service: (new Date()).toISOString().slice(0,10),
                description: '',
                prix: 0,
                capacite: 1,
                type: 'inclus'
            });
            this.editService = this.services.length - 1;
        },
        removeService(index) {
            this.services.splice(index, 1);
            if (this.editService === index) this.editService = null;
        },
        formatDate(dateStr) {
            if (!dateStr) return '-';
            const date = new Date(dateStr);
            return date.toLocaleDateString('fr-FR');
        },
        formatPrix(val) {
            if (val === '' || val === null || val === undefined) return '-';
            return parseFloat(val).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' €';
        },
        isServiceComplete(service) {
            return !!service.type_service
                && !!service.date_service
                && !!service.description.trim()
                && Number(service.prix) > 0
                && Number(service.capacite) > 0
                && !!service.type;
        },
        today: (new Date()).toISOString().slice(0,10),
        capitalize(str) {
            if (!str) return '-';
            return str.charAt(0).toUpperCase() + str.slice(1);
        }
    }
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\angular\Vip_travel\Vip_Travel_Project\resources\views/hotels/confirmation.blade.php ENDPATH**/ ?>