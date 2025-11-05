@extends('admin.layouts.app')

@section('content')

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="container py-4">
    <h2 class="mb-4">Ajouter un Hébergement</h2>

    <form method="POST" action="{{ route('hotels.store') }}" id="hotelForm">
        @csrf
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        

        <!-- Infos générales -->
        <div class="card mb-4">
            <div class="card-header">Informations sur l'hôtel</div>
            <div class="card-body">
                <div class="form-group mb-3">
                    <label for="city">Ville de l'hôtel</label>
                    <input type="text" name="city" id="city" class="form-control" required value="{{ old('city') }}" />
                </div>
                
                <div class="form-group mb-3">
                    <label for="name">Nom de l'hôtel</label>
                    <input type="text" name="name" class="form-control" required value="{{ old('name') }}" />
                </div>
                <div class="form-group">
                    <label for="total_rooms">Nombre total de chambres</label>
                    <input type="number" name="total_rooms" class="form-control" required value="{{ old('total_rooms') }}" />
                </div>
            </div>
        </div>

        <!-- Types de chambre -->
        <div class="card mb-4">
            <div class="card-header">Types de chambres</div>
            <div class="card-body" id="roomTypesContainer">
                @if(old('room_types'))
                    @foreach(old('room_types') as $i => $room)
                        <div class="border p-3 mb-3 room-block">
                            <div class="form-group mb-2">
                                <label>Type de chambre</label>
                                <select name="room_types[{{ $i }}][type]" class="form-control" required>
                                    <option value="single" {{ $room['type'] == 'single' ? 'selected' : '' }}>Single</option>
                                    <option value="double" {{ $room['type'] == 'double' ? 'selected' : '' }}>Double</option>
                                    <option value="triple" {{ $room['type'] == 'triple' ? 'selected' : '' }}>Triple</option>
                                </select>
                            </div>
                            <div class="form-group mb-2">
                                <label>Occupation adulte</label>
                                <input type="number" name="room_types[{{ $i }}][adults]" class="form-control" min="1" required value="{{ $room['adults'] }}" />
                            </div>
                            <div class="form-group mb-2">
                                <label>Enfants (6-11 ans)</label>
                                <input type="number" name="room_types[{{ $i }}][children]" class="form-control" min="0" required value="{{ $room['children'] }}" />
                            </div>
                            <div class="form-group mb-2">
                                <label>Kid (2-4 ans)</label>
                                <input type="number" name="room_types[{{ $i }}][kids]" class="form-control" min="0" required value="{{ $room['kids'] }}" />
                            </div>
                            <div class="form-group mb-2">
                                <label>Bébé (0-2 ans)</label>
                                <input type="number" name="room_types[{{ $i }}][babies]" class="form-control" min="0" required value="{{ $room['babies'] }}" />
                            </div>
                            <div class="form-group mb-2">
                                <label>Prix d'achat (MAD)</label>
                                <input type="number" name="room_types[{{ $i }}][price]" class="form-control" required value="{{ $room['price'] }}" />
                            </div>
                            <div class="form-group mb-2">
                                <label>Chambres en vente</label>
                                <input type="number" name="room_types[{{ $i }}][available_rooms]" class="form-control" required value="{{ $room['available_rooms'] }}" />
                            </div>
                            <div class="form-group mb-2">
                                <label>Pension</label>
                                <select name="room_types[{{ $i }}][pension]" class="form-control">
                                    <option value="RO" {{ $room['pension'] == 'RO' ? 'selected' : '' }}>RO (Room Only)</option>
                                    <option value="PDJ" {{ $room['pension'] == 'PDJ' ? 'selected' : '' }}>PDJ (Petit Déjeuner)</option>
                                    <option value="DP" {{ $room['pension'] == 'DP' ? 'selected' : '' }}>DP (Demi Pension)</option>
                                    <option value="PC" {{ $room['pension'] == 'PC' ? 'selected' : '' }}>PC (Pension Complète)</option>
                                </select>
                            </div>
                            <div class="form-group mt-3 text-end">
                                <button type="button" class="btn btn-danger btn-sm" onclick="removeRoomType(this)">Supprimer cette chambre</button>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
            <div class="card-footer text-end">
                <button type="button" class="btn btn-secondary" onclick="addRoomType()">Ajouter un type de chambre</button>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Enregistrer</button>
    </form>
</div>

<script>
let roomIndex = {{ old('room_types') ? count(old('room_types')) : 0 }};

function addRoomType() {
    const roomContainer = document.getElementById('roomTypesContainer');
    const template = `
        <div class="border p-3 mb-3 room-block">
            <div class="form-group mb-2">
                <label>Type de chambre</label>
                <select name="room_types[${roomIndex}][type]" class="form-control" required>
                    <option value="single">Single</option>
                    <option value="double">Double</option>
                    <option value="triple">Triple</option>
                </select>
            </div>
            <div class="form-group mb-2">
                <label>Occupation adulte</label>
                <input type="number" name="room_types[${roomIndex}][adults]" class="form-control" min="1" required />
            </div>
            <div class="form-group mb-2">
                <label>Enfants (6-11 ans)</label>
                <input type="number" name="room_types[${roomIndex}][children]" class="form-control" min="0" required />
            </div>
            <div class="form-group mb-2">
                <label>Kid (2-4 ans)</label>
                <input type="number" name="room_types[${roomIndex}][kids]" class="form-control" min="0" required />
            </div>
            <div class="form-group mb-2">
                <label>Bébé (0-2 ans)</label>
                <input type="number" name="room_types[${roomIndex}][babies]" class="form-control" min="0" required />
            </div>
            <div class="form-group mb-2">
                <label>Prix d'achat (MAD)</label>
                <input type="number" name="room_types[${roomIndex}][price]" class="form-control" required />
            </div>
            <div class="form-group mb-2">
                <label>Chambres en vente</label>
                <input type="number" name="room_types[${roomIndex}][available_rooms]" class="form-control" required />
            </div>
            <div class="form-group mb-2">
                <label>Pension</label>
                <select name="room_types[${roomIndex}][pension]" class="form-control">
                    <option value="RO">RO (Room Only)</option>
                    <option value="PDJ">PDJ (Petit Déjeuner)</option>
                    <option value="DP">DP (Demi Pension)</option>
                    <option value="PC">PC (Pension Complète)</option>
                </select>
            </div>
            <div class="form-group mt-3 text-end">
                <button type="button" class="btn btn-danger btn-sm" onclick="removeRoomType(this)">Supprimer cette chambre</button>
            </div>
        </div>
    `;
    roomContainer.insertAdjacentHTML('beforeend', template);
    roomIndex++;
}
function removeRoomType(button) {
    const roomBlock = button.closest('.room-block');
    if (roomBlock) {
        roomBlock.remove();
    }
}

</script>
@endsection