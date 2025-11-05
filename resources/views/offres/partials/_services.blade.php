<style>
    .service-cards-pro {
      display: flex;
      flex-wrap: wrap;
      gap: 1.3rem;
    }
    .service-card-pro {
      background: #fff;
      border-radius: 1.05rem;
      box-shadow: 0 3px 18px 0 rgba(56,72,112,0.10);
      border: 1px solid #e5e7eb;
      min-width: 225px;
      max-width: 340px;
      flex: 1 1 240px;
      padding: 1.35rem 1.1rem 1.1rem 1.1rem;
      display: flex;
      flex-direction: column;
      gap: 0.3rem;
      margin-bottom: .8rem;
      position: relative;
      transition: box-shadow .16s, border-color .16s;
    }
    .service-card-pro:hover {
      box-shadow: 0 8px 34px 0 #2e85ff18;
      border-color: #a7c7fa;
    }
    .service-card-pro .service-heading {
      font-weight: 600;
      color: #2563eb;
      font-size: 1.09rem;
      margin-bottom: 0.15rem;
      display: flex;
      align-items: center;
      gap: .5rem;
    }
    .service-card-pro .service-desc {
      color: #475569;
      font-size: .98rem;
      margin-bottom: 0.45rem;
    }
    .service-card-pro .badges {
      display: flex;
      flex-wrap: wrap;
      gap: .42rem;
      margin-bottom: .15rem;
    }
    .service-card-pro .badges .badge {
      font-size: .97rem;
      padding: .32em .9em;
      border-radius: .5em;
    }
    @media (max-width: 800px) {
      .service-cards-pro { flex-direction: column; gap: 1rem; }
      .service-card-pro { max-width: 100%; }
    }
    </style>
    
    @if(!empty($services) && count($services))
    <div class="mb-4">
        <div class="service-cards-pro">
            @foreach($services as $service)
                <div class="service-card-pro">
                    <div class="service-heading">
                        <i class="fa-solid fa-concierge-bell"></i>
                        {{ ucfirst($service['type_service'] ?? $service->type_service ?? '') }}
                    </div>
                    <div class="service-desc">
                        {{ $service['description'] ?? $service->description ?? '' }}
                    </div>
                    <div class="badges">
                        @if(!empty($service['date_service']) || !empty($service->date_service))
                            <span class="badge bg-primary-subtle text-primary">
                                <i class="fa-regular fa-calendar"></i>
                                {{ isset($service['date_service']) ? \Carbon\Carbon::parse($service['date_service'])->format('d/m/Y') : (isset($service->date_service) ? \Carbon\Carbon::parse($service->date_service)->format('d/m/Y') : '') }}
                            </span>
                        @endif
                        @if(!empty($service['prix']) || !empty($service->prix))
                            <span class="badge bg-success-subtle text-success">
                                <i class="fa-solid fa-euro-sign"></i>
                                {{ $service['prix'] ?? $service->prix ?? '' }} €
                            </span>
                        @endif
                        @if(!empty($service['capacite']) || !empty($service->capacite))
                            <span class="badge bg-secondary-subtle text-secondary">
                                <i class="fa-solid fa-users"></i>
                                {{ $service['capacite'] ?? $service->capacite ?? '' }} places
                            </span>
                        @endif
                        @if(!empty($service['type']) || !empty($service->type))
                            <span class="badge bg-light text-dark">
                                <i class="fa-solid fa-tags"></i>
                                {{ $service['type'] ?? $service->type ?? '' }}
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">