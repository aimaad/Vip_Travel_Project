@extends('Layout::admin.app')

@section('content')
    <h2 class="my-4">{{ __('Agency List') }} </h2>
    
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Button to show the add agency form -->
    <button id="show-add-agency-button" class="btn btn-outline-dark mb-3" onclick="toggleAddForm()">{{ __('Add Agency') }}</button>

    <!-- Inline form for adding a new agency, hidden by default -->
    <div id="add-agency-form" style="display:none; background-color: white; padding: 20px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 20px;">
        <h4>{{ __('Add New Agency') }}</h4>
        <form action="{{ route('admin.agencies.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">{{ __('Agency Name:') }}</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>
            <div>
                <button type="submit" class="btn btn-dark">{{ __('Add Agency') }}</button>
                <button type="button" class="btn btn-outline-secondary" onclick="toggleAddForm()">Cancel</button>
            </div>
        </form>
    </div>

    <hr>

    <h4>{{ __('Existing Agencies') }}</h4>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr style="background-color: #f9f9f9;">
                    <th>#</th>
                    <th>{{ __('Name') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($agencies as $agency)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <!-- Normal display mode -->
                            <span id="name-display-{{ $agency->id }}">{{ $agency->name }}</span>

                            <!-- Edit mode form -->
                            <form id="edit-form-{{ $agency->id }}" action="{{ route('admin.agencies.update', $agency) }}" method="POST" style="display:none;">
                                @csrf
                                @method('PUT')
                                <input type="text" name="name" value="{{ $agency->name }}" class="form-control form-control-sm" required>
                                <div class="mt-2">
                                    <button type="submit" class="btn btn-dark btn-sm">{{ __('Save') }}</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="cancelEdit({{ $agency->id }})">{{ __('Cancel') }}</button>
                                </div>
                            </form>
                        </td>
                        <td>
                            <button class="btn btn-outline-dark btn-sm" onclick="toggleEdit({{ $agency->id }})">{{ __('Edit') }}</button>
                            <form action="{{ route('admin.agencies.destroy', $agency) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-dark btn-sm" onclick="return confirm('Are you sure?')">{{ __('Delete') }}</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script>
        function toggleAddForm() {
            var form = document.getElementById('add-agency-form');
            var button = document.getElementById('show-add-agency-button');

            if (form.style.display === 'none' || form.style.display === '') {
                form.style.display = 'block';
                button.style.display = 'none';
            } else {
                form.style.display = 'none';
                button.style.display = 'inline';
            }
        }

        function toggleEdit(id) {
            document.getElementById('name-display-' + id).style.display = 'none';
            document.getElementById('edit-form-' + id).style.display = 'block';
        }

        function cancelEdit(id) {
            document.getElementById('name-display-' + id).style.display = 'block';
            document.getElementById('edit-form-' + id).style.display = 'none';
        }
    </script>
@endsection
