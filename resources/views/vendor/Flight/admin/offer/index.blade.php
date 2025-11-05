@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{__("Flight Offers Management")}}</h3>
                        <div class="card-tools">
                            @if(auth()->user()->hasPermission('flight_offer_create'))
                            <a href="{{route('flight.admin.offers.create')}}" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus"></i> {{__("Create Offer")}}
                            </a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                            <tr>
                                <th>{{__("ID")}}</th>
                                <th>{{__("Type")}}</th>
                                <th>{{__("Flights")}}</th>
                                <th>{{__("Seats")}}</th>
                                <th>{{__("Status")}}</th>
                                <th>{{__("Author")}}</th>
                                <th>{{__("Actions")}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($rows as $row)
                                <tr>
                                    <td>{{$row->id}}</td>
                                    <td>{{ucfirst(str_replace('_', ' ', $row->type))}}</td>
                                    <td>
                                        @foreach($row->flights as $flight)
                                            <span class="badge bg-info">
                                                {{$flight->departure_city}} → {{$flight->arrival_city}} 
                                                ({{$flight->departure_date->format('d/m/Y')}})
                                            </span>
                                        @endforeach
                                    </td>
                                    <td>{{$row->seats_available}}</td>
                                    <td>
                                        <span class="badge bg-{{$row->status === 'confirmed' ? 'success' : 'warning'}}">
                                            {{$row->status}}
                                        </span>
                                    </td>
                                    <td>{{$row->author->name ?? ''}}</td>
                                    <td>
                                        <a href="{{route('flight.admin.offers.edit', $row->id)}}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-danger" onclick="if(confirm('Delete?')){$('#delete-form-{{$row->id}').submit()}">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <form id="delete-form-{{$row->id}}" action="{{route('flight.admin.offers.destroy', $row->id)}}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        {{$rows->links()}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection