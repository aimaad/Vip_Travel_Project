
<link href="<?php echo e(asset('dist/frontend/css/login_register.css?_ver='.config('app.asset_version'))); ?>" rel="stylesheet">


<?php $__env->startSection('content'); ?>
<div class="container auth-page">
    <div class="auth-container">
        <!-- Left Side: Call to Action (for Sign Up or Log In, depending on the state) -->
        <div id="left-side" class="auth-side left-side blue-bg">
            <div class="content">
                <h2 id="leftSideHeading"><?php echo __('Don’t have an account?'); ?></h2>
                <p id="leftSideText"><?php echo __('Create your account and start your journey without delay'); ?></p>
                <button id="toggle-form" class="btn btn-light"><?php echo __('Sign Up'); ?></button>
            </div>
        </div>

        <!-- Right Side: Login Form or Sign-Up Form -->
        <div id="right-side" class="auth-form-container right-side white-bg" >
            <!-- By default, login form is shown -->
            <div id="login-form" class="form-container active">
                <?php echo $__env->make('Layout::admin.message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                <h2 style="margin-bottom:20px;"><?php echo __('Log in to your account'); ?></h2>
                <?php echo $__env->make('Layout::auth.login-form', ['captcha_action'=>'login_normal'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
            <div id="signup-form" class="form-container hidden">
                <h2 style="margin-bottom:20px;" ><?php echo __('Sign up for an account'); ?></h2>
                <?php echo $__env->make('Layout::auth.register-form',['captcha_action'=>'register_normal'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<script src="<?php echo e(asset('libs/tinymce/js/tinymce/auth-toggle.js')); ?>"></script>

<script>
      const translations = {
        loginHeading: `<?php echo __("Don’t have an account?"); ?>`,
        loginText: `<?php echo __("Create your account and start your journey without delay"); ?>`,
        signupHeading: `<?php echo __("Already have an account?"); ?>`,
        signupText: `<?php echo __("Sign in to access your account and continue where you left off."); ?>`,
        signupButton: `<?php echo __("Sign Up"); ?>`,
        loginButton: `<?php echo __("Log In"); ?>`
    };
</script>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\angular\Vip_travel\Vip_Travel_Project\resources\views/auth/login.blade.php ENDPATH**/ ?>