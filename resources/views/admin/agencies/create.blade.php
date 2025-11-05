
@extends("Layout::admin.app")



@section('content')
    <h2>{{ __('Add New Agency') }}</h2>
    
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.agencies.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">{{ __('Agency Name:') }}</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>
        <button type="submit" class="btn btn-primary">{{ __('Add Agency:') }}</button>
    </form>
@endsection
