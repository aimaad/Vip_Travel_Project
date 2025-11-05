<div class="mb-4">
    <h4>Réserver cette offre</h4>
    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <form method="POST" action="<?php echo e(route('offres.reserver', $offre->id)); ?>">
        <?php echo csrf_field(); ?>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Nom complet *</label>
                <input type="text" name="nom_client" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Email *</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Téléphone</label>
                <input type="text" name="telephone" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
                <label>Nombre de personnes *</label>
                <input type="number" name="nombre_personnes" class="form-control" required min="1" value="1">
            </div>
            <div class="col-md-6 mb-3">
                <label>Date d'arrivée *</label>
                <input type="date" name="date_arrivee" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Date de départ *</label>
                <input type="date" name="date_depart" class="form-control" required>
            </div>
            <div class="col-12 mb-3">
                <label>Commentaire</label>
                <textarea name="commentaire" class="form-control"></textarea>
            </div>
        </div>
        <button class="btn btn-success" type="submit">Envoyer la demande</button>
    </form>
</div><?php /**PATH C:\angular\Vip_travel\Vip_Travel_Project\resources\views/offres/partials/_reservation_form.blade.php ENDPATH**/ ?>