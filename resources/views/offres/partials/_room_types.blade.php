<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
.room-cards-pro {
  display: flex;
  flex-wrap: wrap;
  gap: 1.5rem;
  margin-bottom: 2rem;
}
.room-card-pro {
  flex: 1 1 240px;
  max-width: 340px;
  background: #fff;
  border-radius: 1.2rem;
  box-shadow: 0 4px 18px 0 rgba(56,72,112,0.10);
  border: 1px solid #e5e7eb;
  padding: 1.6rem 1.3rem 1.1rem 1.3rem;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  transition: box-shadow .18s, border-color .18s;
  position: relative;
}
.room-card-pro:hover {
  box-shadow: 0 8px 32px 0 #2e85ff18;
  border-color: #a7c7fa;
}
.room-card-pro .icon {
  font-size: 2.2rem;
  color: #2563eb;
  background: #eff4fa;
  border-radius: 50%;
  width: 52px;
  height: 52px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: .8rem;
  box-shadow: 0 1px 6px 0 rgba(56,72,112,0.08);
}
.room-card-pro .type-title {
  font-weight: 600;
  color: #1f2937;
  font-size: 1.18rem;
  margin-bottom: .4rem;
  text-transform: capitalize;
  letter-spacing: .5px;
}
.room-card-pro .sub {
  color: #6b7280;
  font-size: .97rem;
  margin-bottom: .8rem;
}
.room-card-pro .badges {
  display: flex;
  flex-wrap: wrap;
  gap: .45rem;
  margin-bottom: .7rem;
}
.room-card-pro .badges .badge {
  font-size: .97rem;
  padding: .32em .9em;
  border-radius: .5em;
}
.room-card-pro .price {
  margin-top: .7rem;
  font-weight: 600;
  color: #059669;
  background: #e7fbe9;
  border-radius: .5em;
  padding: .36em 1.1em;
  font-size: 1.15rem;
  letter-spacing: .5px;
  box-shadow: 0 1px 4px 0 rgba(5,150,105,0.09);
}
@media (max-width: 800px) {
  .room-cards-pro { flex-direction: column; gap: 1.1rem; }
  .room-card-pro { max-width: 100%; }
}
</style>

<div class="room-cards-pro">
@foreach($types as $room)
    @php
        $type = strtolower($room['type'] ?? '');
        $icons = [
            'single' => '<i class="fa-solid fa-person icon"></i>',
            'double' => '<i class="fa-solid fa-people-arrows icon"></i>',
            'triple' => '<i class="fa-solid fa-people-group icon"></i>',
        ];
        $icon = $icons[$type] ?? '<i class="fa-solid fa-bed icon"></i>';
    @endphp
    <div class="room-card-pro">
        {!! $icon !!}
        <div class="type-title">{{ ucfirst($type) }}</div>
        @if(isset($room['pension']))
            <div class="sub">{{ $room['pension'] }}</div>
        @endif
        <div class="badges">
            <span class="badge bg-primary-subtle text-primary">
                <i class="fa-solid fa-user"></i>
                {{ $room['adults'] ?? '-' }} Adulte{{ ($room['adults'] ?? 0) > 1 ? 's' : '' }}
            </span>
            @if(!empty($room['children']))
            <span class="badge bg-warning-subtle text-warning">
                <i class="fa-solid fa-child"></i>
                {{ $room['children'] }} Enfant{{ $room['children'] > 1 ? 's' : '' }}
            </span>
            @endif
            @if(!empty($room['babies']))
            <span class="badge bg-success-subtle text-success">
                <i class="fa-solid fa-baby"></i>
                {{ $room['babies'] }} Bébé{{ $room['babies'] > 1 ? 's' : '' }}
            </span>
            @endif
        </div>
        <div class="price">
            {{ $room['price'] ?? '-' }} €
        </div>
    </div>
@endforeach
</div>