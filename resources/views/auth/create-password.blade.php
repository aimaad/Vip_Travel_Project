@extends('layouts.app')

@section('content')
<div class="container">
    <h2>{{ __('Create Your Password') }}</h2>
    <form method="POST" action="{{ route('auth.password.store') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="form-group">
            <label for="password">{{ __('Password') }}</label>
            <input type="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="password_confirmation">{{ __('Confirm Password') }}</label>
            <input type="password" name="password_confirmation" required>
        </div>
        <button type="submit">{{ __('Create Password') }}</button>
    </form>
</div>
@endsection
