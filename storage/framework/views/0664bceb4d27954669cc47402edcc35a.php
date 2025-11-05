<div class="panel">
    <div class="panel-title"><strong><?php echo e(__("Tour Content")); ?></strong></div>
    <div class="panel-body">
        <div class="form-group " data-id="title" data-type="title">
            <label class="control-label"><?php echo e(__("Title")); ?></label>
            <input type="text" value="<?php echo e(old('title',$translation->title)); ?>" placeholder="<?php echo e(__("Title")); ?>" name="title" class="form-control">
        </div>
        <div class="form-group" data-id="content" data-type="content">
            <label class="control-label"><?php echo e(__("Content")); ?></label>
            <div class="">
                <textarea name="content" class="d-none has-ckeditor" id="content" cols="30" rows="10"><?php echo e(old('content',$translation->content)); ?></textarea>
            </div>
        </div>
        <div class="form-group d-none">
            <label class="control-label"><?php echo e(__("Description")); ?></label>
            <div class="">
                <textarea name="short_desc" class="form-control" cols="30" rows="4"><?php echo e(old('short_desc',$translation->short_desc)); ?></textarea>
            </div>
        </div>
        <?php if(is_default_lang()): ?>
          
           


            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label class="control-label"><?php echo e(__("Tour Date")); ?></label>
                        <input type="date" name="tour_date" class="form-control " value="<?php echo e(old('tour_date',$row->tour_date)); ?>" placeholder="<?php echo e(__("Tour Date")); ?>">
                    </div>
                </div>
                
                
                <div class="col-lg-6">
                    <div class="form-group">
                        <label class="control-label"><?php echo e(__("Category")); ?></label>
                        <div class="">
                            <select name="category_id" class="form-control">
                                <option value=""><?php echo e(__("-- Please Select --")); ?></option>
                                <?php
                                $traverse = function ($categories, $prefix = '') use (&$traverse, $row) {
                                    foreach ($categories as $category) {
                                        $selected = '';
                                        if ($row->category_id == $category->id)
                                            $selected = 'selected';
                                        printf("<option value='%s' %s>%s</option>", $category->id, $selected, $prefix . ' ' . $category->name);
                                        $traverse($category->children, $prefix . '-');
                                    }
                                };
                                $traverse($tour_category);
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                <div class="form-group">
                    <label class="control-label"><?php echo e(__("Duration (in days)")); ?></label>
                    <input type="number" name="duration" class="form-control" value="<?php echo e(old('duration', $row->duration)); ?>" placeholder="<?php echo e(__("Enter duration in days")); ?>">
                </div>
            </div>
               
                
            </div>

        <?php endif; ?>
        <?php do_action(\Modules\Tour\Hook::FORM_AFTER_MAX_PEOPLE,$row) ?>
        <div class="form-group-item">
            <label class="control-label"><?php echo e(__('FAQs')); ?></label>
            <div class="g-items-header">
                <div class="row">
                    <div class="col-md-5"><?php echo e(__("Title")); ?></div>
                    <div class="col-md-5"><?php echo e(__('Content')); ?></div>
                    <div class="col-md-1"></div>
                </div>
            </div>
            <div class="g-items">
                <?php if(!empty($translation->faqs)): ?>
                    <?php if(!is_array($translation->faqs)) $translation->faqs = json_decode(old('faqs',$translation->faqs)); ?>
                    <?php $__currentLoopData = $translation->faqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="item" data-number="<?php echo e($key); ?>">
                            <div class="row">
                                <div class="col-md-5">
                                    <input type="text" name="faqs[<?php echo e($key); ?>][title]" class="form-control" value="<?php echo e($faq['title']); ?>" placeholder="<?php echo e(__('Eg: When and where does the tour end?')); ?>">
                                </div>
                                <div class="col-md-6">
                                    <textarea name="faqs[<?php echo e($key); ?>][content]" class="form-control full-h" placeholder="..."><?php echo e($faq['content']); ?></textarea>
                                </div>
                                <div class="col-md-1">
                                    <span class="btn btn-danger btn-sm btn-remove-item"><i class="fa fa-trash"></i></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </div>
            <div class="text-right">
                <span class="btn btn-info btn-sm btn-add-item"><i class="icon ion-ios-add-circle-outline"></i> <?php echo e(__('Add item')); ?></span>
            </div>
            <div class="g-more hide">
                <div class="item" data-number="__number__">
                    <div class="row">
                        <div class="col-md-5">
                            <input type="text" __name__="faqs[__number__][title]" class="form-control" placeholder="<?php echo e(__('Eg: When and where does the tour end?')); ?>">
                        </div>
                        <div class="col-md-6">
                            <textarea __name__="faqs[__number__][content]" class="form-control full-h" placeholder="..."></textarea>
                        </div>
                        <div class="col-md-1">
                            <span class="btn btn-danger btn-sm btn-remove-item"><i class="fa fa-trash"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php echo $__env->make('Tour::admin/tour/include-exclude', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php echo $__env->make('Tour::admin/tour/itinerary', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php if(is_default_lang()): ?>
            <div class="form-group">
                <label class="control-label"><?php echo e(__("Banner Image")); ?></label>
                <div class="form-group-image">
                    <?php echo \Modules\Media\Helpers\FileHelper::fieldUpload('banner_image_id',old('banner_image_id',$row->banner_image_id)); ?>

                </div>
            </div>
            <div class="form-group">
                <label class="control-label"><?php echo e(__("Gallery")); ?></label>
                <?php echo \Modules\Media\Helpers\FileHelper::fieldGalleryUpload('gallery',old('gallery',$row->gallery)); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH C:\angular\Vip_travel\Vip_Travel_Project\modules/Tour/Views/admin/tour/tour-content.blade.php ENDPATH**/ ?>