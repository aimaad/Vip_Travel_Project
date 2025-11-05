@extends('layouts.user')

@section('content')

    <h2 class="title-bar">
        {{ $role_name === 'Agent de voyage' ? __("Become an Agent") : __("Become a Distributor") }}
    </h2>

    {{-- Affichage des messages d'erreur --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Affichage des messages d'alerte --}}
    @if (session('alert'))
        <div class="alert alert-danger">
            {{ session('alert') }}
        </div>
    @endif

    {{-- Affichage des messages de confirmation --}}
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('info'))
        <div class="alert alert-info">
            {{ session('info') }}
        </div>
    @endif

    <form action="{{ route('user.changeRole') }}" method="POST">
        @csrf
        <input type="hidden" name="role_name" value="{{ $role_name }}">

        <div class="form-group">
            <label>{{ __("Nom d'agence (obligatoire):") }}</label>
            <select id="agency_name" name="agency_name" class="form-control select2 agency-select" required>
                <option value=""></option>
                @foreach($agencies as $agency)
                    <option value="{{ $agency }}" {{ old('agency_name', auth()->user()->agency_name) == $agency ? 'selected' : '' }}>{{ $agency }}</option>
                @endforeach
                <option value="other" {{ old('agency_name', auth()->user()->agency_name) == 'other' || auth()->user()->agency_name == 'other' ? 'selected' : '' }}>{{ __("Other (please specify below)") }}</option>
            </select>
        </div>

        <div class="form-group" id="other-agency-field" style="{{ old('agency_name', auth()->user()->agency_name) == 'other' || auth()->user()->agency_name == 'other' ? 'display:block;' : 'display:none;' }}">
            <label>{{ __("Ecrire votre nom d'agence:") }}</label>
            <input type="text" id="other_agency_name" name="other_agency_name" class="form-control" value="{{ old('other_agency_name', auth()->user()->agency_name) }}">
        </div>

        <div class="form-group">
            <label>{{ __("IATA OU Office Id (non obligatoire):") }}</label>
            <input type="text" id="iata_office_id" name="iata_office_id" class="form-control" value="{{ old('iata_office_id', auth()->user()->iata_office_id) }}">
        </div>

        <button type="submit" class="btn btn-primary">{{ $role_name === 'Agent de voyage' ? __("Become an Agent") : __("Become a Distributor") }}</button>
    </form>

@endsection

@push('scripts')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $('select#agency_name').on('change', function() {
            if ($(this).val() === 'other') {
                $('#other-agency-field').show();
                $('#other_agency_name').prop('required', true);
            } else {
                $('#other-agency-field').hide();
                $('#other_agency_name').prop('required', false);
            }
        });
    </script>
@endpush
