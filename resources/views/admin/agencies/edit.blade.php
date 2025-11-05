@extends("Layout::admin.app")



@section('content')
    <h2>Edit Agency</h2>
    
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.agencies.update', $agency) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">{{ __('Agency Name:') }}</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $agency->name) }}" required>
        </div>
        <button type="submit" class="btn btn-primary">{{ __('Update Agency') }}</button>
    </form>
@endsection
