<?php
    $images = $scraping->images ?? [];
    if (is_string($images)) $images = json_decode($images, true) ?? [];
    // Miniatures = max800, carrousel = max1600 (ou max1024 si 1600 indisponible)
    function toThumb($url) { return str_replace('max300', 'max800', $url); }
    function toBig($url) { 
        // Essaye max1600, sinon max1024
        if(strpos($url, 'max1600') !== false) return $url;
        if(strpos($url, 'max1024') !== false) return $url;
        $url = str_replace('max300', 'max1600', $url);
        if(strpos($url, 'max1600') !== false) return $url;
        return str_replace('max1600', 'max1024', $url); // fallback
    }
    $imagesThumbs = array_map('toThumb', $images);
    $imagesBig = array_map('toBig', $images);
    $main        = $imagesThumbs[0] ?? null;
    $topRight    = $imagesThumbs[1] ?? null;
    $middleRight = $imagesThumbs[2] ?? null;
    $bottom      = array_slice($imagesThumbs, 3, 5);
    $total = count($images);
    $extraCount = $total > 8 ? $total - 8 : 0;
?>

<style>
    .booking-gallery-grid {
        display: grid;
        grid-template-columns: 547px 273px;
        grid-template-rows: 176px 176px;
        gap: 8px;
        margin-bottom: 8px;
        min-width: 820px;
        max-width: 100%;
    }
    .booking-gallery-grid .main-img,
    .booking-gallery-grid .top-right,
    .booking-gallery-grid .middle-right {
        width: 100%;
        object-fit: cover;
        border-radius: 14px;
        background: #ececec;
        cursor: pointer;
    }
    .booking-gallery-grid .main-img { grid-row: 1 / span 2; grid-column: 1; aspect-ratio: 547/359; }
    .booking-gallery-grid .top-right { grid-row: 1; grid-column: 2; aspect-ratio: 273/176; }
    .booking-gallery-grid .middle-right { grid-row: 2; grid-column: 2; aspect-ratio: 273/176; }
    .booking-gallery-bottom {
        display: flex;
        gap: 8px;
        margin-top: 2px;
        flex-wrap: wrap;
        min-width: 820px;
    }
    .booking-gallery-bottom img {
        width: 159px; height: 103px;
        object-fit: cover;
        border-radius: 10px;
        cursor: pointer;
        background: #ececec;
    }
    .booking-gallery-plus {
        position: relative;
        cursor: pointer;
    }
    .booking-gallery-plus span {
        position: absolute; left:0; top:0; width:100%; height:100%;
        background:rgba(0,0,0,0.53); color:#fff;
        font-weight:600; font-size:1.1rem;
        border-radius:10px;
        display:flex; align-items:center; justify-content:center;
        transition:background .2s;
        pointer-events: none;
    }
    .booking-gallery-plus:hover span { background:rgba(0,0,0,0.7);}
    @media (max-width: 991px) {
        .booking-gallery-grid {
            grid-template-columns: 1fr 0.5fr;
            grid-template-rows: 88px 88px;
            min-width: unset;
        }
        .booking-gallery-grid .main-img { width:100%; height: 180px; }
        .booking-gallery-grid .top-right, .booking-gallery-grid .middle-right { width:100%; height:88px;}
        .booking-gallery-bottom { min-width: unset; }
        .booking-gallery-bottom img { width: 75px; height: 48px;}
    }
    @media (max-width: 600px) {
        .booking-gallery-grid { display: block; }
        .booking-gallery-grid img { width: 100% !important; height: auto !important; margin-bottom: 6px;}
        .booking-gallery-bottom { flex-wrap: wrap; gap:4px;}
        .booking-gallery-bottom img { width:48%; height: 40px;}
    }
    /* Carrousel flèches noires */
    .carousel-control-prev-icon,
    .carousel-control-next-icon {
        filter: invert(1) grayscale(1) brightness(0.2);
    }
    
</style>

<div>
    <div class="booking-gallery-grid mb-2" style="overflow-x:auto;">
        <?php if($main): ?>
            <img src="<?php echo e($main); ?>" class="main-img shadow-sm"
                 alt="photo" data-bs-toggle="modal"
                 data-bs-target="#galleryModalCarousel"
                 data-img-index="0">
        <?php endif; ?>
        <?php if($topRight): ?>
            <img src="<?php echo e($topRight); ?>" class="top-right shadow-sm"
                 alt="photo" data-bs-toggle="modal"
                 data-bs-target="#galleryModalCarousel"
                 data-img-index="1">
        <?php endif; ?>
        <?php if($middleRight): ?>
            <img src="<?php echo e($middleRight); ?>" class="middle-right shadow-sm"
                 alt="photo" data-bs-toggle="modal"
                 data-bs-target="#galleryModalCarousel"
                 data-img-index="2">
        <?php endif; ?>
    </div>
    <div class="booking-gallery-bottom" style="overflow-x:auto;">
        <?php $__currentLoopData = $bottom; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $globalIndex = $i + 3; ?>
            <?php if($i === 4 && $extraCount > 0): ?>
                <div class="booking-gallery-plus"
                     data-bs-toggle="modal"
                     data-bs-target="#galleryModalCarousel"
                     data-img-index="<?php echo e($globalIndex); ?>">
                    <img src="<?php echo e($img); ?>" alt="photo">
                    <span>+<?php echo e($extraCount); ?> photos</span>
                </div>
            <?php else: ?>
                <img src="<?php echo e($img); ?>" alt="photo"
                     data-bs-toggle="modal"
                     data-bs-target="#galleryModalCarousel"
                     data-img-index="<?php echo e($globalIndex); ?>">
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>

<!-- Bootstrap Modal with Carousel -->
<div class="modal fade" id="galleryModalCarousel" tabindex="-1" aria-labelledby="galleryModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content bg-white" style="border-radius:1.2rem;">
      <div class="modal-header border-0">
        <h5 class="modal-title" id="galleryModalLabel">Galerie photos</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      <div class="modal-body">
        <div id="galleryCarousel" class="carousel slide" data-bs-ride="false">
          <div class="carousel-inner">
            <?php $__currentLoopData = $imagesBig; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="carousel-item<?php echo e($idx === 0 ? ' active' : ''); ?>">
              <img src="<?php echo e($img); ?>" alt="photo"
                   class="d-block w-100 rounded shadow-sm"
                   style="max-height: 540px; object-fit:contain; margin:auto;">
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </div>
          <button class="carousel-control-prev" type="button" data-bs-target="#galleryCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Précédent</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#galleryCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Suivant</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var modal = document.getElementById('galleryModalCarousel');
    var carousel = document.getElementById('galleryCarousel');
    var carouselInstance = null;
    function getCarouselInstance() {
        if (!window.bootstrap) return null;
        if (bootstrap.Carousel.getOrCreateInstance)
            return bootstrap.Carousel.getOrCreateInstance(carousel, { interval: false });
        return new bootstrap.Carousel(carousel, { interval: false });
    }
    function goToSlide(idx) {
        carouselInstance = getCarouselInstance();
        if (carouselInstance && typeof carouselInstance.to === 'function') {
            carouselInstance.to(idx);
        }
    }
    document.querySelectorAll('[data-bs-target="#galleryModalCarousel"][data-img-index]').forEach(function(el){
        el.addEventListener('click', function(){
            var idx = parseInt(el.getAttribute('data-img-index'), 10) || 0;
            setTimeout(function(){
                goToSlide(idx);
            }, 350);
        });
    });
    modal.addEventListener('hidden.bs.modal', function () {
        goToSlide(0);
    });
});
</script><?php /**PATH C:\angular\Vip_Travel_Project\resources\views/offres/partials/_gallery.blade.php ENDPATH**/ ?>