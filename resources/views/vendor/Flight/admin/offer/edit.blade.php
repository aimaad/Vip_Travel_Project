@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{__("Verify Flight Offer")}}</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{route('flight.admin.offers.update', $row->id)}}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__("Seats Available")}} *</label>
                                        <input type="number" name="seats_available" class="form-control" 
                                               value="{{$row->seats_available}}" required min="1">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__("Status")}}</label>
                                        <select name="status" class="form-control">
                                            <option value="draft" {{$row->status === 'draft' ? 'selected' : ''}}>{{__("Draft")}}</option>
                                            <option value="confirmed" {{$row->status === 'confirmed' ? 'selected' : ''}}>{{__("Confirmed")}}</option>
                                            <option value="canceled" {{$row->status === 'canceled' ? 'selected' : ''}}>{{__("Canceled")}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <h4 class="mt-4">{{__("Flight Information")}}</h4>
                            
                            @foreach($row->flights as $flight)
                                <div class="card card-secondary mb-3">
                                    <div class="card-header">
                                        <h5 class="card-title">
                                            {{$flight->departure_city}} → {{$flight->arrival_city}} 
                                            ({{$flight->departure_date->format('d/m/Y H:i')}})
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        @if($flight->flight_data)
                                            <div class="flight-details">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <p><strong>{{__("Flight Number")}}:</strong> {{$flight->flight_number}}</p>
                                                        <p><strong>{{__("Airline")}}:</strong> {{$flight->flight_data['validatingAirlineCodes'][0] ?? 'N/A'}}</p>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <p><strong>{{__("Departure")}}:</strong> {{$flight->departure_date->format('d/m/Y H:i')}}</p>
                                                        <p><strong>{{__("Arrival")}}:</strong> {{$flight->flight_data['itineraries'][0]['segments'][0]['arrival']['at'] ?? 'N/A'}}</p>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <p><strong>{{__("Duration")}}:</strong> {{$flight->flight_data['itineraries'][0]['duration'] ?? 'N/A'}}</p>
                                                        <p><strong>{{__("Aircraft")}}:</strong> {{$flight->flight_data['itineraries'][0]['segments'][0]['aircraft']['code'] ?? 'N/A'}}</p>
                                                    </div>
                                                </div>
                                                
                                                <h5 class="mt-3">{{__("Pricing")}}</h5>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <p><strong>{{__("Price")}}:</strong> {{$flight->flight_data['price']['total'] ?? 'N/A'}} {{$flight->flight_data['price']['currency'] ?? ''}}</p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p><strong>{{__("Seats Left")}}:</strong> {{$flight->flight_data['numberOfBookableSeats'] ?? 'N/A'}}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="alert alert-warning">
                                                {{__("No flight data available. Please check the flight information.")}}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                            
                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary">{{__("Save Offer")}}</button>
                                <a href="{{route('flight.admin.offers.index')}}" class="btn btn-default">{{__("Cancel")}}</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection