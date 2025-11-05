<div class="panel">
    <div class="panel-title"><strong><?php echo e(__('Publishing Options')); ?></strong></div>
    <div class="panel-body">
        <!-- Publish Now Button -->
        <label class="radio-inline">
            <input type="radio" name="publish_option" value="publish_now" 
                <?php if($row->status == 'publish'): ?> checked <?php endif; ?> required>
            <?php echo e(__('Publish Now')); ?>

        </label>

        <!-- Save as Draft Button -->
        <label class="radio-inline">
            <input type="radio" name="publish_option" value="save_draft" 
                <?php if($row->status == 'draft' || empty($row->status)): ?> checked <?php endif; ?> required>
            <?php echo e(__('Save as Draft')); ?>

        </label>

        <!-- Schedule Button -->
        <label class="radio-inline">
            <input type="radio" name="publish_option" value="schedule" 
                <?php if($row->status == 'scheduled'): ?> checked <?php endif; ?> required>
            <?php echo e(__('Schedule')); ?>

        </label>

        <!-- Date pickers for scheduling (only visible when scheduling is selected) -->
        <div id="schedule-options" style="display: none; display: flex; gap: 15px; margin-top:50px !important;">
            <div class="form-group">
                <label><?php echo e(__('Publish Date')); ?></label>
                <input type="date" class="form-control" name="publish_date" 
                    value="<?php echo e(old('publish_date', $row->publish_date ? \Carbon\Carbon::parse($row->publish_date)->format('Y-m-d') : '')); ?>" 
                    placeholder="<?php echo e(__('Select when the tour should be published')); ?>">
            </div>
            <div class="form-group">
                <label><?php echo e(__('Draft Date')); ?></label>
                <input type="date" class="form-control" name="draft_date" 
                    value="<?php echo e(old('draft_date', $row->draft_date ? \Carbon\Carbon::parse($row->draft_date)->format('Y-m-d') : '')); ?>" 
                    placeholder="<?php echo e(__('Select when the tour should be drafted')); ?>">
            </div>
        </div>
    </div>
</div>



<?php $__currentLoopData = $attributes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attribute): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php $translate = $attribute->translate(app_get_locale()); ?>
    <div class="panel">
        <div class="panel-title"><strong><?php echo e(__('Attribute: :name',['name'=>$translate->name])); ?></strong></div>
        <div class="panel-body">
            <div class="terms-scrollable">
                <?php $__currentLoopData = $attribute->terms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $term): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $term_translate = $term->translate(app_get_locale()); ?>
                    <label class="term-item">
                        <input <?php if(!empty($selected_terms) and $selected_terms->contains($term->id)): ?> checked <?php endif; ?> type="checkbox" name="terms[]" value="<?php echo e($term->id); ?>">
                        <span class="term-name"><?php echo e($term_translate->name); ?></span>
                    </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<!-- Include jQuery (make sure this is before any script that uses jQuery) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
   $(document).ready(function () {
    // Initial setup: hide or show schedule options based on selected option
    toggleScheduleOptions($('input[name="publish_option"]:checked').val());

    // Listen for changes in the publish_option radio buttons
    $('input[name="publish_option"]').on('change', function () {
        toggleScheduleOptions($(this).val());
    });

    function toggleScheduleOptions(option) {
        if (option === 'schedule') {
            $('#schedule-options').show();
            $('input[name="publish_date"], input[name="draft_date"]').prop('required', true);
        } else {
            $('#schedule-options').hide();
            $('input[name="publish_date"], input[name="draft_date"]').prop('required', false).val(''); // Reset values to null
        }
    }
});

 </script>
 
<?php /**PATH C:\angular\Vip_travel\Vip_Travel_Project\modules/Tour/Views/admin/tour/attributes.blade.php ENDPATH**/ ?>