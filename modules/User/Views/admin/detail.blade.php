@extends('admin.layouts.app')

@section('content')
    <form action="{{ route('user.admin.store', ['id' => $row->id ?? -1]) }}" method="post" class="needs-validation" novalidate>
        @csrf
        <div class="container">
            <div class="d-flex justify-content-between mb20">
                <div class="">
                    <h1 class="title-bar">
                        {{ $row->id ? __('Edit: :name', ['name' => $row->getDisplayName()]) : __('Add new user') }}
                    </h1>
                </div>
                
            </div>
            @include('admin.message')
            <div class="row">
                <div class="col-md-9">
                    <div class="panel">
                        <div class="panel-title"><strong>{{ __('User Info') }}</strong></div>
                        <div class="panel-body">
                            <div class="row">
                             <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __("Civility") }}</label>
                                    <div>
                                        <label><input type="radio" name="civilite" value="M" {{ old('civilite', $row->civilite) == 'M' ? 'checked' : '' }}> M</label>
                                        <label><input type="radio" name="civilite" value="Mme" {{ old('civilite', $row->civilite) == 'Mme' ? 'checked' : '' }}> Mme</label>
                                        <label><input type="radio" name="civilite" value="Ms" {{ old('civilite', $row->civilite) == 'Ms' ? 'checked' : '' }}> Ms</label>
                                    </div>
                                </div>
                            </div>
                                

                                <!-- First Name -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __("First name") }}</label>
                                        <input type="text" required value="{{ old('first_name', $row->first_name) }}" name="first_name" placeholder="{{ __("First name") }}" class="form-control">
                                    </div>
                                </div>

                                <!-- Last Name -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __("Last name") }}</label>
                                        <input type="text" required value="{{ old('last_name', $row->last_name) }}" name="last_name" placeholder="{{ __("Last name") }}" class="form-control">
                                    </div>
                                </div>

                                <!-- Email -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('E-mail') }}</label>
                                        <input type="email" required value="{{ old('email', $row->email) }}" placeholder="{{ __('Email') }}" name="email" class="form-control">
                                    </div>
                                </div>

                                <!-- Phone Number -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Phone Number') }}</label>
                                        <input type="text" value="{{ old('phone', $row->phone) }}" placeholder="{{ __('Phone') }}" name="phone" class="form-control" required>
                                    </div>
                                </div>

                                <!-- Date of Birth -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Birthday') }}</label>
                                        <input type="text" value="{{ old('birthday', $row->birthday ? date('Y/m/d', strtotime($row->birthday)) : '') }}" placeholder="{{ __('Birthday') }}" name="birthday" class="form-control has-datepicker input-group date">
                                    </div>
                                </div>

                                <!-- City -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __("City") }}</label>
                                        <select name="city" class="form-control">
                                            <option value="">{{ __('-- Select a City --') }}</option>
                                            @php
                                            $cities = [
                                                'Agadir', 'Aguelmous', 'Ait Baha', 'Ait Melloul', 'Ait Ourir', 'Al Hoceima', 'Al Aaiún',
                                                'Asilah', 'Assa', 'Azemmour', 'Azilal', 'Azrou', 'Beni Mellal', 'Ben Guerir', 'Benslimane',
                                                'Berkane', 'Berrechid', 'Bouarfa', 'Boujdour', 'Boulemane', 'Bouznika', 'Casablanca', 'Chefchaouen',
                                                'Chichaoua', 'Dakhla', 'El Aaiún', 'El Jadida', 'El Kelaa des Sraghna', 'Errachidia', 'Essaouira',
                                                'Fes', 'Figuig', 'Gourrama', 'Guelmim', 'Guercif', 'Ifrane', 'Inezgane', 'Imouzzer Kandar',
                                                'Jorf', 'Jerada', 'Kenitra', 'Khemisset', 'Khouribga', 'Ksar El Kebir',
                                                'Larache', 'Laayoune', 'Martil', 'Marrakech', 'Meknes', 'Midelt', 'Missour', 'Mohammedia',
                                                'Nador', 'Ouarzazate', 'Oued Zem', 'Oujda', 'Oulad Teima', 'Ouazzane', 'Rabat', 'Safi',
                                                'Sefrou', 'Settat', 'Sidi Bennour', 'Sidi Ifni', 'Sidi Kacem', 'Sidi Slimane', 'Sidi Yahya El Gharb',
                                                'Skhirat', 'Smara', 'Tafraout', 'Tafraoute', 'Tan-Tan', 'Tanger', 'Taourirt', 'Taounate',
                                                'Taroudant', 'Tata', 'Taza', 'Temara', 'Tetouan', 'Tiflet', 'Tinghir', 'Tiznit', 
                                                'Youssoufia', 'Zagora', 'Zawyat an Nwacer', 'Zerhoun'
                                            ];
                                            @endphp
                                            @foreach($cities as $city)
                                                <option value="{{ $city }}" @if(old('city', $row->city) == $city) selected @endif>{{ $city }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <!-- Agency Name (Select or Text Input) -->
                                <div class="col-md-6 agent-distributor-fields">
                                    <div class="form-group">
                                        <label>{{ __("Agency Name") }}</label>
                                        <select id="agency_name" name="agency_name" class="form-control">
                                            <option value="">{{ __('-- Select an Agency --') }}</option>
                                            @foreach($agencies as $agency)
                                                <option value="{{ $agency->name }}" @if(old('agency_name', $row->agency_name) == $agency->name) selected @endif>
                                                    {{ $agency->name }}
                                                </option>
                                            @endforeach
                                            <option value="other" @if(!in_array($row->agency_name, $agencies->pluck('name')->toArray())) selected @endif>{{ __('Other') }}</option>
                                        </select>
                                        <input type="text" id="other_agency_name" name="other_agency_name" class="form-control mt-2" placeholder="{{ __('Enter your agency name') }}" value="{{ !in_array($row->agency_name, $agencies->pluck('name')->toArray()) ? $row->agency_name : '' }}" style="display: none;">
                                    </div>
                                </div>

                                <!-- IATA Office ID -->
                                <div class="col-md-6 agent-distributor-fields">
                                    <div class="form-group">
                                        <label>{{ __("IATA Office ID") }}</label>
                                        <input type="text" value="{{ old('iata_office_id', $row->iata_office_id) }}" name="iata_office_id" placeholder="{{ __("IATA Office ID") }}" class="form-control">
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Sidebar -->
                <div class="col-md-3">
                    <div class="panel">
                        <div class="panel-title"><strong>{{ __('Publish') }}</strong></div>
                        <div class="panel-body">
                            
                            @if(is_admin())
                                @if(empty($user_type) or $user_type != 'vendor')
                                    <div class="form-group">
                                        <label>{{ __('Role') }} <span class="text-danger">*</span></label>
                                        <select required class="form-control" name="role_id" id="role-select">
                                            <option value="">{{ __('-- Select --') }}</option>
                                            @foreach($roles as $role)
                                                <option value="{{ $role->id }}" @if(old('role_id', $row->role_id) == $role->id) selected @elseif(old('role_id') == $role->id) selected @elseif(request()->input('user_type') == strtolower($role->name)) selected @endif>{{ ucfirst($role->name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                                <div class="form-group">
                                    <label>{{ __('Email Verified?') }}</label>
                                    <select class="form-control" name="is_email_verified">
                                        <option value="">{{ __('No') }}</option>
                                        <option @if(old('is_email_verified', $row->email_verified_at ? 1 : 0)) selected @endif value="1">{{ __('Yes') }}</option>
                                    </select>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="panel">
                        <div class="panel-title"><strong>{{ __('Vendor') }}</strong></div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label>{{ __('Vendor Commission Type') }}</label>
                                <div class="form-controls">
                                    <select name="vendor_commission_type" class="form-control">
                                        <option value="">{{ __("Default") }}</option>
                                        <option value="percent" {{ old("vendor_commission_type", $row->vendor_commission_type) == 'percent' ? 'selected' : '' }}>{{ __('Percent') }}</option>
                                        <option value="amount" {{ old("vendor_commission_type", $row->vendor_commission_type) == 'amount' ? 'selected' : '' }}>{{ __('Amount') }}</option>
                                        <option value="disable" {{ old("vendor_commission_type", $row->vendor_commission_type) == 'disable' ? 'selected' : '' }}>{{ __('Disable Commission') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>{{ __('Vendor commission value') }}</label>
                                <div class="form-controls">
                                    <input type="text" class="form-control" name="vendor_commission_amount" value="{{ old("vendor_commission_amount", $row->vendor_commission_amount) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel">
                        <div class="panel-title"><strong>{{ __('Avatar') }}</strong></div>
                        <div class="panel-body">
                            <div class="form-group">
                                {!! \Modules\Media\Helpers\FileHelper::fieldUpload('avatar_id', old('avatar_id', $row->avatar_id)) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="d-flex justify-content-between">
                <span></span>
                <button class="btn btn-primary" type="submit">{{ __('Save Change') }}</button>
            </div>
        </div>
    </form>

    <script>
  document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role-select');
    const agencySelect = document.getElementById('agency_name');
    const otherAgencyInput = document.getElementById('other_agency_name');
    const agentDistributorFields = document.querySelectorAll('.agent-distributor-fields');

    function toggleAgentDistributorFields() {
        if (roleSelect) {
            const selectedRole = roleSelect.options[roleSelect.selectedIndex].text.toLowerCase();
            if (selectedRole.includes('agent de voyage') || selectedRole.includes('distributeur de voyage')) {
                agentDistributorFields.forEach(function(field) {
                    field.style.display = 'block';
                });
            } else {
                agentDistributorFields.forEach(function(field) {
                    field.style.display = 'none';
                });
                // Clear fields when not needed
                agentDistributorFields.forEach(function(field) {
                    const inputs = field.querySelectorAll('input, select');
                    inputs.forEach(input => {
                        input.value = '';  // Clear the value to avoid unwanted submissions
                        input.required = false;  // Ensure fields are not required if hidden
                    });
                });
            }
        }
    }

    function toggleAgencyFields() {
        if (agencySelect && otherAgencyInput) {
            if (agencySelect.value === 'other') {
                otherAgencyInput.style.display = 'block';
                otherAgencyInput.required = true;
            } else {
                otherAgencyInput.style.display = 'none';
                otherAgencyInput.required = false;
            }
        }
    }

    // Add event listeners to update fields based on role and agency selection
    if (roleSelect) {
        roleSelect.addEventListener('change', toggleAgentDistributorFields);
    }

    if (agencySelect) {
        agencySelect.addEventListener('change', toggleAgencyFields);
    }

    // Execute functions on page load
    toggleAgentDistributorFields();
    toggleAgencyFields();
});

    </script>
@endsection