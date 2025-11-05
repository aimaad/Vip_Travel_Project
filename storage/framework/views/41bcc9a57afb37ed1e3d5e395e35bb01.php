
<?php $__env->startSection('content'); ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    body { background-color: #f8fafc; }
    .modern-shadow { box-shadow: 0 8px 32px 0 rgba(56,72,112,0.09); }
    .hotel-main-card { border-radius: 1.5rem; background: #fff; }
    .hotel-header-modern {
        background: #fff;
        border-radius: 1.4rem 1.4rem 0 0;
        border-bottom: 1px solid #f1f5f9;
        box-shadow: 0 2px 12px 0 rgba(56,72,112,0.04);
        padding: 2rem 2rem 1.2rem 2rem;
        margin-bottom: 0.5rem;
        display: flex;
        flex-direction: column;
        gap: 0.7rem;
    }
    .hotel-title-modern {
        font-size: 2.1rem;
        color: #2563eb;
        letter-spacing: -1px;
        font-weight: 700;
        margin-bottom: 0;
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }
    .rating-badge-modern {
        background: #f6f7fd;
        color: #2563eb;
        border-radius: .85rem;
        font-weight: 600;
        padding: .45em 1em;
        display: inline-flex;
        align-items: center;
        box-shadow: 0 2px 8px rgba(56,72,112,0.08);
        font-size: 1.13rem;
        margin-left: 0.5rem;
    }
    .hotel-address-modern {
        color: #2563eb;
        font-size: 1.07rem;
        font-weight: 500;
        opacity: .82;
        display: flex;
        align-items: center;
        gap: 0.35rem;
        margin-top: 0.2rem;
    }
    .section-title { font-weight: 600; color: #1f2937; margin-bottom: 1rem; font-size: 1.18rem; }
    .badge-price { background-color: #d1fae5; color: #059669; font-size: 1rem; padding: .4rem .7rem; border-radius: .5rem; }
    @media (min-width: 992px) {
        .right-sticky { position: sticky; top: 40px; }
        .hotel-header-modern { flex-direction: row; align-items: flex-end; justify-content: space-between; }
        .hotel-header-modern > div:first-child { flex: 1 1 auto; }
    }
    .owner-block { border-radius: 1rem; background: linear-gradient(90deg,#f1f5f9 80%,#e0e7ef 100%); box-shadow: 0 8px 32px 0 rgba(56,72,112,0.07); padding:1.6rem 1rem 1rem 1rem; margin-bottom: 1.5rem; cursor: pointer; transition: box-shadow .2s; }
    .owner-block:hover { box-shadow: 0 12px 40px 0 rgba(56,72,112,0.16);}
    .owner-avatar { width:52px; height:52px; border-radius:50%; object-fit:cover; border:2px solid #60a5fa; box-shadow: 0 1px 4px rgba(56,72,112,.12);}
    .owner-name { font-weight:600; color:#2563eb; font-size:1.08rem;}
    .owner-contact { color: #64748b; font-size:.97rem;}
    .reservation-block { border-radius: 1rem; background: #fff; box-shadow: 0 6px 28px 0 rgba(56,72,112,0.09);}
    @media (max-width: 991px) {
        .right-sticky { position: static; top: unset; }
        .hotel-header-modern { padding: 1.2rem 1rem 1rem 1rem !important; }
        .hotel-title-modern { font-size: 1.2rem; }
    }
    .show-offre-margin-top {
        margin-top: 3.5rem !important;
    }
    @media (max-width: 991px) {
        .show-offre-margin-top {
            margin-top: 2.4rem !important;
        }
    }

    /* Chambres modernes */
    .room-list-modern {
        display: flex;
        flex-wrap: wrap;
        gap: 1.6rem;
        margin-bottom: 2rem;
    }
    .room-card-modern {
        background: #f9fbfe;
        border-radius: 1.1rem;
        box-shadow: 0 4px 20px 0 rgba(56,72,112,0.08);
        padding: 1.7rem 1.2rem 1.2rem 1.2rem;
        min-width: 230px;
        flex: 1 1 260px;
        max-width: 320px;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        position: relative;
        transition: box-shadow .18s;
        border: 1px solid #e7eaf3;
    }
    .room-card-modern:hover {
        box-shadow: 0 8px 40px 0 #2e85ff27;
        border-color: #2e85ff33;
    }
    .room-icon-modern {
        font-size: 2.2rem;
        margin-bottom: 0.7rem;
        color: #2e85ff;
        background: #e5f0fd;
        border-radius: 50%;
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .room-type-modern {
        font-weight: bold;
        color: #2563eb;
        font-size: 1.19rem;
        margin-bottom: .4rem;
        text-transform: capitalize;
        letter-spacing: .5px;
    }
    .room-badges-modern .badge {
        margin-right: 0.5em;
        font-size: 1rem;
    }
    .room-badges-modern {
        margin-top: 0.6rem;
    }
</style>

<?php
    $scraping = $offre->hotel_scraping;
    $types = $offre->room_types ?? [];
    if (!is_array($types)) $types = json_decode($types, true) ?? [];
    $owner = $offre->creator;
    $services = $offre->services ?? [];
    if (!is_array($services)) $services = json_decode($services, true) ?? [];
?>

<div class="container py-5 show-offre-margin-top">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-white px-3 py-2 rounded shadow-sm">
            <li class="breadcrumb-item">
                <a href="<?php echo e(url('/')); ?>" class="text-decoration-none text-primary">Accueil</a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?php echo e(route('offres.index')); ?>" class="text-decoration-none text-primary">Offres</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <?php echo e($scraping->hotel_name ?? 'Offre'); ?>

            </li>
        </ol>
    </nav>
    <div class="row g-4">
        <!-- LEFT: 3/4 -->
        <div class="col-lg-9">
            <div class="hotel-main-card modern-shadow">
                <!-- Nouveau header moderne -->
                <div class="hotel-header-modern">
                    <div>
                        <div class="hotel-title-modern">
                            <svg width="30" height="30" fill="none" stroke="#2563eb" stroke-width="2" viewBox="0 0 24 24"><path d="M4 11V9a8 8 0 0116 0v2"/><rect width="20" height="11" x="2" y="11" rx="2"/><path d="M8 15h.01M16 15h.01"/></svg>
                            <?php echo e($scraping->hotel_name ?? 'Hôtel Inconnu'); ?>

                            <?php if($scraping->rating): ?>
                                <span class="badge rating-badge-modern">
                                    <svg width="20" height="20" fill="#fbbf24" viewBox="0 0 24 24"><path d="M12 17.5l6.16 3.73-1.64-7.03L21.82 9.5l-7.19-.61L12 2.5l-2.63 6.39-7.19.61 5.3 4.7-1.64 7.03z"/></svg>
                                    <span class="ms-1 fs-6"><?php echo e($scraping->rating); ?>/10</span>
                                </span>
                            <?php endif; ?>
                        </div>
                        <?php if($scraping->address): ?>
                        <div class="hotel-address-modern">
                            <svg width="18" height="18" fill="#2563eb" stroke="#2563eb" stroke-width="2" viewBox="0 0 24 24"><path d="M12 21C16.4183 21 20 17.4183 20 13C20 8.58172 16.4183 5 12 5C7.58172 5 4 8.58172 4 13C4 17.4183 7.58172 21 12 21Z"/><circle cx="12" cy="13" r="4"/></svg>
                            <span><?php echo e($scraping->address); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php echo $__env->make('offres.partials._gallery', ['scraping' => $scraping], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                <div class="p-4">
                    <h4 class="section-title">Types de chambres disponibles</h4>
                    <?php echo $__env->make('offres.partials._room_types', ['types' => $types], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>

                
                <div class="p-4">
                    <h4 class="section-title">Services proposés</h4>
                    <?php echo $__env->make('offres.partials._services', ['services' => $services], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>

            </div>
            <div class="flight-section">
                <h4 class="section-title"><i class="fa fa-plane-departure"></i> Détails des vols</h4>

                <?php $__currentLoopData = $offre->offerFlights; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $flight): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="bg-white p-4 rounded shadow-sm border mb-4">
                      
                        
                        <div class="row text-center mb-3">
                            <div class="col">
                                <div class="fw-bold text-secondary"><i class="fa fa-user text-primary"></i> Adulte</div>
                                <div class="text-success fs-5"><?php echo e(number_format($flight->price_adult, 2)); ?> €</div>
                            </div>
                            <div class="col">
                                <div class="fw-bold text-secondary"><i class="fa fa-child text-primary"></i> Enfant</div>
                                <div class="text-success fs-5"><?php echo e(number_format($flight->price_child, 2)); ?> €</div>
                            </div>
                            <div class="col">
                                <div class="fw-bold text-secondary"><i class="fa fa-baby text-primary"></i> Bébé</div>
                                <div class="text-success fs-5"><?php echo e(number_format($flight->price_baby, 2)); ?> €</div>
                            </div>
                            <div class="col">
                                <div class="fw-bold text-secondary"><i class="fa fa-chair text-primary"></i> Places dispo</div>
                                <div class="text-dark fs-5"><?php echo e($flight->places); ?></div>
                            </div>
                        </div>
                
                        
                        <?php $__currentLoopData = $flight->flightLegs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $leg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="border rounded p-3 mb-3">
                                <h6 class="text-secondary fw-bold">
                                    <i class="fa fa-location-arrow"></i> <?php echo e(ucfirst($leg->direction)); ?>

                                </h6>
                                <ul class="list-unstyled mb-0">
                                    <li><strong>Vol :</strong> <?php echo e($leg->flight_number); ?></li>
                                    <li><strong>Compagnie :</strong>
                                        <?php if($leg->airline_logo): ?>
                                            <img src="<?php echo e($leg->airline_logo); ?>" style="height: 24px; vertical-align: middle;">
                                        <?php else: ?>
                                            <span class="text-muted">Non renseignée</span>
                                        <?php endif; ?>
                                    </li>
                                    <li><strong>Départ :</strong> <?php echo e($leg->departure_city); ?> — <?php echo e($leg->departure_date); ?> à <?php echo e($leg->departure_time); ?></li>
                                    <li><strong>Arrivée :</strong> <?php echo e($leg->arrival_city); ?> — <?php echo e($leg->arrival_date); ?> à <?php echo e($leg->arrival_time); ?></li>
                                </ul>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                
            </div>
        </div>
        <!-- RIGHT: 1/4 -->
        <div class="col-lg-3">
            <div class="right-sticky">
                <!-- OWNER BLOCK -->
                <a href="<?php echo e(route('user.profile', ['id' => $owner->user_name ?? $owner->id])); ?>" class="text-decoration-none">
                    <div class="owner-block">
                        <div class="d-flex align-items-center gap-3">
                            <img src="<?php echo e($owner->getAvatarUrl()); ?>" alt="Avatar" class="owner-avatar">
                            <div>
                                <div class="owner-name"><?php echo e($owner->getDisplayName()); ?></div>
                                <div class="owner-contact">
                                    Membre depuis <?php echo e(\Carbon\Carbon::parse($owner->created_at)->translatedFormat('F Y')); ?>

                                </div>
                            </div>
                        </div>
                    </div>
                </a>
                
                <!-- RESERVATION FORM BLOCK -->
                <div class="reservation-block p-3 shadow-sm">
                    <?php echo $__env->make('offres.partials._reservation_form', ['offre' => $offre], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<style>
    .flight-section {
    background: #fff;
    border-radius: 1.25rem;
    box-shadow: 0 8px 24px rgb(0 0 0 / 0.08);
    padding: 2rem 2.5rem;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #333;
    margin-top: 3rem;
}

.flight-section h4 {
    font-weight: 700;
    color: #2563eb;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.6rem;
    font-size: 1.8rem;
}

.flight-section h5 {
    font-weight: 600;
    color: #1e293b;
    border-bottom: 2px solid #e0e7ff;
    padding-bottom: 0.5rem;
    margin-bottom: 1rem;
}

.flight-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0 0.8rem;
}

.flight-table th, .flight-table td {
    padding: 0.6rem 1rem;
    vertical-align: middle;
    font-size: 1rem;
}

.flight-table th {
    width: 40%;
    color: #64748b;
    font-weight: 600;
    text-align: left;
}

.flight-table td {
    background: #f1f5f9;
    border-radius: 0.75rem;
    color: #334155;
}

.flight-table tr:hover td {
    background: #e0e7ff;
    transition: background-color 0.3s ease;
}

.airline-logo {
    height: 45px;
    object-fit: contain;
    border-radius: 0.5rem;
    box-shadow: 0 2px 8px rgb(37 99 235 / 0.25);
}

.price-section {
    margin-top: 2.5rem;
    border-top: 2px solid #e0e7ff;
    padding-top: 1.8rem;
    display: flex;
    gap: 2rem;
    justify-content: flex-start;
    flex-wrap: wrap;
}

.price-item {
    background: #eff6ff;
    padding: 1rem 1.5rem;
    border-radius: 1rem;
    min-width: 120px;
    text-align: center;
    box-shadow: 0 4px 20px rgb(37 99 235 / 0.15);
    cursor: default;
    transition: box-shadow 0.3s ease;
}

.price-item:hover {
    box-shadow: 0 6px 32px rgb(37 99 235 / 0.3);
}

.price-item .label {
    font-weight: 600;
    color: #2563eb;
    margin-bottom: 0.3rem;
    font-size: 1.1rem;
}

.price-item .amount {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1e40af;
}

@media (max-width: 767px) {
    .flight-section {
        padding: 1.5rem 1.8rem;
    }
    .price-section {
        justify-content: center;
    }
}
    </style>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\angular\Vip_Travel_Project\resources\views/offres/show.blade.php ENDPATH**/ ?>