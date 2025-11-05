@extends('layouts.app')
<link href="{{ asset('dist/frontend/css/login_register.css?_ver='.config('app.asset_version')) }}" rel="stylesheet">


@section('content')
<div class="container auth-page">
    <div class="auth-container">
        <!-- Left Side: Call to Action (for Sign Up or Log In, depending on the state) -->
        <div id="left-side" class="auth-side left-side blue-bg">
            <div class="content">
                <h2 id="leftSideHeading">{!! __('Don’t have an account?') !!}</h2>
                <p id="leftSideText">{!! __('Create your account and start your journey without delay') !!}</p>
                <button id="toggle-form" class="btn btn-light">{!! __('Sign Up') !!}</button>
            </div>
        </div>

        <!-- Right Side: Login Form or Sign-Up Form -->
        <div id="right-side" class="auth-form-container right-side white-bg" >
            <!-- By default, login form is shown -->
            <div id="login-form" class="form-container active">
                @include('Layout::admin.message')

                <h2 style="margin-bottom:20px;">{!! __('Log in to your account') !!}</h2>
                @include('Layout::auth.login-form', ['captcha_action'=>'login_normal'])
            </div>
            <div id="signup-form" class="form-container hidden">
                <h2 style="margin-bottom:20px;" >{!! __('Sign up for an account') !!}</h2>
                @include('Layout::auth.register-form',['captcha_action'=>'register_normal'])
            </div>
        </div>
    </div>
</div>
@endsection
<script src="{{ asset('libs/tinymce/js/tinymce/auth-toggle.js') }}"></script>

<script>
      const translations = {
        loginHeading: `{!! __("Don’t have an account?") !!}`,
        loginText: `{!! __("Create your account and start your journey without delay") !!}`,
        signupHeading: `{!! __("Already have an account?") !!}`,
        signupText: `{!! __("Sign in to access your account and continue where you left off.") !!}`,
        signupButton: `{!! __("Sign Up") !!}`,
        loginButton: `{!! __("Log In") !!}`
    };
</script>
