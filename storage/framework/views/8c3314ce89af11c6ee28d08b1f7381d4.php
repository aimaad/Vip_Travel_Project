
<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-header">
        <h3>Ajouter une compagnie</h3>
    </div>
    <div class="card-body">
        <?php if($errors->any()): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <form id="airline-form" action="<?php echo e(route('admin.airlines.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Code IATA (2 lettres)</label>
                        <input type="text" name="iata_code" 
                               class="form-control <?php $__errorArgs = ['iata_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               value="<?php echo e(old('iata_code')); ?>" 
                               required maxlength="2">
                        <?php $__errorArgs = ['iata_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nom complet</label>
                        <input type="text" name="name" 
                               class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               value="<?php echo e(old('name')); ?>" 
                               required>
                        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label>Domaine (ex: airfrance.com)</label>
                <input type="text" name="domain" 
                       class="form-control <?php $__errorArgs = ['domain'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                       value="<?php echo e(old('domain')); ?>" 
                       required>
                <?php $__errorArgs = ['domain'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <small class="text-muted">Utilisé pour générer le logo via Clearbit</small>
            </div>
            
            <div class="form-group text-center my-4">
                <div id="logo-preview" class="mb-3" style="display: none;">
                    <img id="logo-image" src="" alt="Logo preview" class="img-thumbnail" style="max-height: 100px;">
                </div>
                <button type="button" id="preview-logo" class="btn btn-secondary">
                    <i class="icon ion-ios-eye"></i> Prévisualiser le logo
                </button>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="icon ion-ios-save"></i> Enregistrer
            </button>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion de la prévisualisation du logo
    const previewBtn = document.getElementById('preview-logo');
    if (previewBtn) {
        previewBtn.addEventListener('click', function() {
            const domain = document.querySelector('input[name="domain"]').value.trim();
            if (!domain) {
                alert("Veuillez entrer un domaine valide (ex: airfrance.com)");
                return;
            }

            const logoUrl = `https://logo.clearbit.com/${domain}?size=150`;
            const logoImage = document.getElementById('logo-image');
            
            // Test de chargement de l'image
            const testImage = new Image();
            testImage.onload = function() {
                logoImage.src = logoUrl;
                document.getElementById('logo-preview').style.display = 'block';
            };
            testImage.onerror = function() {
                alert("Logo non trouvé pour ce domaine");
            };
            testImage.src = logoUrl;
        });
    }

    // Vérification en temps réel des doublons
    const domainInput = document.querySelector('input[name="domain"]');
    const iataInput = document.querySelector('input[name="iata_code"]');
    
    function checkExisting(field, value) {
        if (!value || value.length < (field === 'iata_code' ? 2 : 5)) return;
        
        fetch(`/admin/airlines/check-existing?${field}=${encodeURIComponent(value)}`)
            .then(response => response.json())
            .then(data => {
                const input = document.querySelector(`input[name="${field}"]`);
                const feedback = input.nextElementSibling;
                
                if (data.exists) {
                    input.classList.add('is-invalid');
                    if (!feedback || !feedback.classList.contains('invalid-feedback')) {
                        const newFeedback = document.createElement('div');
                        newFeedback.className = 'invalid-feedback';
                        newFeedback.textContent = field === 'iata_code' 
                            ? 'Ce code IATA existe déjà' 
                            : 'Ce domaine est déjà utilisé';
                        input.after(newFeedback);
                    }
                } else {
                    input.classList.remove('is-invalid');
                    if (feedback && feedback.classList.contains('invalid-feedback')) {
                        feedback.remove();
                    }
                }
            });
    }
    
    if (domainInput) {
        domainInput.addEventListener('blur', function() {
            checkExisting('domain', this.value);
        });
    }
    
    if (iataInput) {
        iataInput.addEventListener('blur', function() {
            checkExisting('iata_code', this.value);
        });
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make("Layout::admin.app", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\angular\Vip_travel\Vip_Travel_Project\resources\views/admin/airlines/create.blade.php ENDPATH**/ ?>