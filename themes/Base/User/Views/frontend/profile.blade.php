@extends('layouts.user')
@section('content')
    <h2 class="title-bar">
        {{__("Settings")}}
        <a href="{{route('user.change_password')}}" class="btn-change-password">{{__("Change Password")}}</a>
    </h2>
    @include('admin.message')
    <form action="{{route('user.profile.update')}}" method="post" enctype="multipart/form-data" class="input-has-icon">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <div class="form-title">
                    <strong>{{__("Personal Information")}}</strong>
                </div>
                
                <!-- Civilite -->
                <div class="form-group">
                    <label>{{__("Civilité")}}</label>
                    <div>
                        <label><input type="radio" name="civilite" value="M" {{ old('civilite', $dataUser->civilite) == 'M' ? 'checked' : '' }}> M</label>
                        <label><input type="radio" name="civilite" value="Mme" {{ old('civilite', $dataUser->civilite) == 'Mme' ? 'checked' : '' }}> Mme</label>
                        <label><input type="radio" name="civilite" value="Ms" {{ old('civilite', $dataUser->civilite) == 'Ms' ? 'checked' : '' }}> Ms</label>
                    </div>
                </div>

                <!-- First Name -->
                <div class="form-group">
                    <label>{{__("First Name")}}</label>
                    <input type="text" value="{{old('first_name',$dataUser->first_name)}}" name="first_name" placeholder="{{__("First Name")}}" class="form-control">
                    <i class="fa fa-user input-icon"></i>
                </div>

                <!-- Last Name -->
                <div class="form-group">
                    <label>{{__("Last Name")}}</label>
                    <input type="text" value="{{old('last_name',$dataUser->last_name)}}" name="last_name" placeholder="{{__("Last Name")}}" class="form-control">
                    <i class="fa fa-user input-icon"></i>
                </div>

                <!-- Date of Birth -->
                <div class="form-group">
                    <label>{{__("Date of Birth")}}</label>
                    <input type="text" value="{{ old('birthday',$dataUser->birthday? display_date($dataUser->birthday) :'') }}" name="birthday" placeholder="{{__("Date of Birth")}}" class="form-control date-picker">
                    <i class="fa fa-calendar input-icon"></i>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label>{{__("E-mail")}}</label>
                    <input type="text" name="email" value="{{old('email',$dataUser->email)}}" placeholder="{{__("E-mail")}}" class="form-control">
                    <i class="fa fa-envelope input-icon"></i>
                </div>

                <!-- Phone Number -->
                <div class="form-group">
                    <label>{{__("Phone Number")}}</label>
                    <input type="text" value="{{old('phone',$dataUser->phone)}}" name="phone" placeholder="{{__("Phone Number")}}" class="form-control">
                    <i class="fa fa-phone input-icon"></i>
                </div>

                <!-- City -->
                <div class="form-group">
                    <label>{{__("City")}}</label>
                    <select name="city" class="form-control">
                        <option value="">{{__('-- Select City --')}}</option>
                        @foreach($cities as $city)
                            <option @if((old('city',$dataUser->city ?? '')) == $city) selected @endif value="{{$city}}">{{$city}}</option>
                        @endforeach
                    </select>
                </div>
 <!-- Avatar Upload -->
 <div class="form-group">
    <label>{{__("Avatar")}}</label>
    <div class="upload-btn-wrapper">
        <div class="input-group">
            <span class="input-group-btn">
                <span class="btn btn-default btn-file">
                    {{__("Browse")}}… <input type="file" name="avatar">
                </span>
            </span>
            <input type="text" data-error="{{__("Error upload...")}}" data-loading="{{__("Loading...")}}" class="form-control text-view" readonly value="{{ get_file_url( old('avatar_id',$dataUser->avatar_id) ) ?? $dataUser->getAvatarUrl()?? __("No Image")}}">
        </div>
        <input type="hidden" class="form-control" name="avatar_id" value="{{ old('avatar_id',$dataUser->avatar_id)?? ""}}">
        <img class="image-demo" src="{{ get_file_url( old('avatar_id',$dataUser->avatar_id) ) ??  $dataUser->getAvatarUrl() ?? ""}}"/>
    </div>
</div>

                <div class="col-md-12">
                    <hr>
                    <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> {{__('Save Changes')}}</button>
                </div>
            </div>
        </div>
    </form>
@endsection
