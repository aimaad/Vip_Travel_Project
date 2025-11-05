@extends('layouts.app')

@section('content')
<div class="container" style="margin: 8rem auto;">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="text-center mb-4">
                <h3>{{ __('Set Your New Password') }}</h3>
            </div>
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <form method="POST" action="{{ route('user.savePassword', [
                        'id' => $id,
                        'hash' => $hash,
                        'expires' => $expires,
                        'signature' => $signature
                    ]) }}">
                        @csrf
                        
                        <!-- Nouveau mot de passe -->
                        <div class="form-group mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                            <!-- Afficher les erreurs de validation du mot de passe -->
                            @error('password')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <!-- Confirmation du mot de passe -->
                        <div class="form-group mb-3">
                            <label for="password_confirmation" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation" required>
                            <!-- Afficher les erreurs de validation de confirmation -->
                            @error('password_confirmation')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Case à cocher pour accepter les termes -->
                        <div class="form-group mb-3">
                            <label for="term" class="form-label">
                                <input type="checkbox" id="term" name="term" required>
                                {!! __("I have read and accept the <a href=':link' target='_blank'>Terms and Privacy Policy</a>", ['link'=>get_page_url(setting_item('booking_term_conditions'))]) !!}
                            </label>
                            <!-- Afficher l'erreur si la case à cocher n'est pas cochée -->
                            @error('term')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Bouton de soumission -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-block">
                                Set Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
