

<?php $__env->startSection('content'); ?>

<?php if(session('success')): ?>
    <div class="alert alert-success">
        <?php echo e(session('success')); ?>

    </div>
<?php endif; ?>

<div class="container py-4">
    <h2 class="mb-4">Ajouter un Hébergement</h2>

    <form method="POST" action="<?php echo e(route('hotels.store')); ?>" id="hotelForm">
        <?php echo csrf_field(); ?>
        <?php if($errors->any()): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>
        

        <!-- Infos générales -->
        <div class="card mb-4">
            <div class="card-header">Informations sur l'hôtel</div>
            <div class="card-body">
                <div class="form-group mb-3">
                    <label for="city">Ville de l'hôtel</label>
                    <input type="text" name="city" id="city" class="form-control" required value="<?php echo e(old('city')); ?>" />
                </div>
                
                <div class="form-group mb-3">
                    <label for="name">Nom de l'hôtel</label>
                    <input type="text" name="name" class="form-control" required value="<?php echo e(old('name')); ?>" />
                </div>
                <div class="form-group">
                    <label for="total_rooms">Nombre total de chambres</label>
                    <input type="number" name="total_rooms" class="form-control" required value="<?php echo e(old('total_rooms')); ?>" />
                </div>
            </div>
        </div>

        <!-- Types de chambre -->
        <div class="card mb-4">
            <div class="card-header">Types de chambres</div>
            <div class="card-body" id="roomTypesContainer">
                <?php if(old('room_types')): ?>
                    <?php $__currentLoopData = old('room_types'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="border p-3 mb-3 room-block">
                            <div class="form-group mb-2">
                                <label>Type de chambre</label>
                                <select name="room_types[<?php echo e($i); ?>][type]" class="form-control" required>
                                    <option value="single" <?php echo e($room['type'] == 'single' ? 'selected' : ''); ?>>Single</option>
                                    <option value="double" <?php echo e($room['type'] == 'double' ? 'selected' : ''); ?>>Double</option>
                                    <option value="triple" <?php echo e($room['type'] == 'triple' ? 'selected' : ''); ?>>Triple</option>
                                </select>
                            </div>
                            <div class="form-group mb-2">
                                <label>Occupation adulte</label>
                                <input type="number" name="room_types[<?php echo e($i); ?>][adults]" class="form-control" min="1" required value="<?php echo e($room['adults']); ?>" />
                            </div>
                            <div class="form-group mb-2">
                                <label>Enfants (6-11 ans)</label>
                                <input type="number" name="room_types[<?php echo e($i); ?>][children]" class="form-control" min="0" required value="<?php echo e($room['children']); ?>" />
                            </div>
                            <div class="form-group mb-2">
                                <label>Kid (2-4 ans)</label>
                                <input type="number" name="room_types[<?php echo e($i); ?>][kids]" class="form-control" min="0" required value="<?php echo e($room['kids']); ?>" />
                            </div>
                            <div class="form-group mb-2">
                                <label>Bébé (0-2 ans)</label>
                                <input type="number" name="room_types[<?php echo e($i); ?>][babies]" class="form-control" min="0" required value="<?php echo e($room['babies']); ?>" />
                            </div>
                            <div class="form-group mb-2">
                                <label>Prix d'achat (MAD)</label>
                                <input type="number" name="room_types[<?php echo e($i); ?>][price]" class="form-control" required value="<?php echo e($room['price']); ?>" />
                            </div>
                            <div class="form-group mb-2">
                                <label>Chambres en vente</label>
                                <input type="number" name="room_types[<?php echo e($i); ?>][available_rooms]" class="form-control" required value="<?php echo e($room['available_rooms']); ?>" />
                            </div>
                            <div class="form-group mb-2">
                                <label>Pension</label>
                                <select name="room_types[<?php echo e($i); ?>][pension]" class="form-control">
                                    <option value="RO" <?php echo e($room['pension'] == 'RO' ? 'selected' : ''); ?>>RO (Room Only)</option>
                                    <option value="PDJ" <?php echo e($room['pension'] == 'PDJ' ? 'selected' : ''); ?>>PDJ (Petit Déjeuner)</option>
                                    <option value="DP" <?php echo e($room['pension'] == 'DP' ? 'selected' : ''); ?>>DP (Demi Pension)</option>
                                    <option value="PC" <?php echo e($room['pension'] == 'PC' ? 'selected' : ''); ?>>PC (Pension Complète)</option>
                                </select>
                            </div>
                            <div class="form-group mt-3 text-end">
                                <button type="button" class="btn btn-danger btn-sm" onclick="removeRoomType(this)">Supprimer cette chambre</button>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </div>
            <div class="card-footer text-end">
                <button type="button" class="btn btn-secondary" onclick="addRoomType()">Ajouter un type de chambre</button>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Enregistrer</button>
    </form>
</div>

<script>
let roomIndex = <?php echo e(old('room_types') ? count(old('room_types')) : 0); ?>;

function addRoomType() {
    const roomContainer = document.getElementById('roomTypesContainer');
    const template = `
        <div class="border p-3 mb-3 room-block">
            <div class="form-group mb-2">
                <label>Type de chambre</label>
                <select name="room_types[${roomIndex}][type]" class="form-control" required>
                    <option value="single">Single</option>
                    <option value="double">Double</option>
                    <option value="triple">Triple</option>
                </select>
            </div>
            <div class="form-group mb-2">
                <label>Occupation adulte</label>
                <input type="number" name="room_types[${roomIndex}][adults]" class="form-control" min="1" required />
            </div>
            <div class="form-group mb-2">
                <label>Enfants (6-11 ans)</label>
                <input type="number" name="room_types[${roomIndex}][children]" class="form-control" min="0" required />
            </div>
            <div class="form-group mb-2">
                <label>Kid (2-4 ans)</label>
                <input type="number" name="room_types[${roomIndex}][kids]" class="form-control" min="0" required />
            </div>
            <div class="form-group mb-2">
                <label>Bébé (0-2 ans)</label>
                <input type="number" name="room_types[${roomIndex}][babies]" class="form-control" min="0" required />
            </div>
            <div class="form-group mb-2">
                <label>Prix d'achat (MAD)</label>
                <input type="number" name="room_types[${roomIndex}][price]" class="form-control" required />
            </div>
            <div class="form-group mb-2">
                <label>Chambres en vente</label>
                <input type="number" name="room_types[${roomIndex}][available_rooms]" class="form-control" required />
            </div>
            <div class="form-group mb-2">
                <label>Pension</label>
                <select name="room_types[${roomIndex}][pension]" class="form-control">
                    <option value="RO">RO (Room Only)</option>
                    <option value="PDJ">PDJ (Petit Déjeuner)</option>
                    <option value="DP">DP (Demi Pension)</option>
                    <option value="PC">PC (Pension Complète)</option>
                </select>
            </div>
            <div class="form-group mt-3 text-end">
                <button type="button" class="btn btn-danger btn-sm" onclick="removeRoomType(this)">Supprimer cette chambre</button>
            </div>
        </div>
    `;
    roomContainer.insertAdjacentHTML('beforeend', template);
    roomIndex++;
}
function removeRoomType(button) {
    const roomBlock = button.closest('.room-block');
    if (roomBlock) {
        roomBlock.remove();
    }
}

</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\angular\Vip_travel\Vip_Travel_Project\resources\views/hotels/create.blade.php ENDPATH**/ ?>