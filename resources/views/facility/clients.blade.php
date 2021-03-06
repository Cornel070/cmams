@extends('layouts.dashboard')
@section('content')
@section('sweet-alert-area')
    <script src="{{asset('assets/js/sweetalert2.js')}}" defer></script>
@endsection

<!-- [ stiped-table ] start -->
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Clients in {{$facility->name}} facility</h5>
                        <span class="d-block m-t-5">There are a total of <b><code>{{ number_format($facility->clients()->count()) }}</code></b> client(s) in this facility</span>
                        <button type="button" class="btn btn-info btn-sm add-btn" data-toggle="modal" data-target="#add-client-form">
                            <i class="la la-plus-circle"></i> Add Client to {{$facility->name}}</button>
                    </div>
 
                    <div class="card-body table-border-style">
                        <div class="table-responsive">
                        
                            <table class="table table-striped" id="entry-table">
                                <thead>
                                    <tr>
                                        <th>Hospital No.</th>
                                        <th>Gender</th>
                                        <th>Facility</th>
                                        <th>Case Manager</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($facility->clientsPaginate() as $client)
                                    <tr id="de-{{$client->id}}">
                                        <td>{{$client->hospital_num}}</td>
                                        <td>{{$client->sex}}</td>
                                        <td>{{$client->facility}}</td>
                                        <td>{{$client->case_manager}}</td>
                                        <td>{{$client->status}}</td>
                                        <td>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <button type="button" class="btn btn-info btn-sm" data-toggle="tooltip" title="Edit client info" onclick="window.location.href='{{route('edit-client',$client->id)}}'"><i class="la la-edit"></i>
                                                    </button>
                                                </div>
                                                <div class="col-md-4">
                                                <span data-toggle="modal" data-target="#cl{{$client->id}}">
                                                    <button type="button" class="btn btn-primary btn-sm" data-toggle="tooltip" data-placement="top" title="View client summary">
                                                        <i class="la la-eye"></i>
                                                    </button>
                                                </span>
                                                </div>
                                                <div class="col-md-4">
                                                    <button type="button" id="{{$client->id}}" class="btn btn-danger btn-sm delete-btn-client" data-toggle="tooltip" title="Delete client info">
                                                        <i class="la la-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $facility->clientsPaginate()->links() }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- View Facility Modal --}}
        @foreach($facility->clients() as $client)
        <div class="modal fade" id="cl{{$client->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel">
                    <i class="la la-user"></i> {{$client->name}} 
                </h4><span class="badge badge-pill badge-info client-status"> {{$client->status}}</span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <table class="table table-bordered table-striped">
                                        <tr>
                                            <th>Client ID</th>
                                            <td>{{ $client->clientID }}</td>
                                        </tr>
                                        <tr>
                                            <th>Client Name</th>
                                            <td>{{ $client->name}}</td>
                                        </tr>
                                        <tr>
                                            <th>Phone Number</th>
                                            <td>{{$client->phone}}</td>
                                        </tr>
                                        <tr>
                                            <th>Phone OPC</th>
                                            <td>{{$client->opc_phone}}</td>
                                        </tr>
                                        <tr>
                                            <th>Residential Address </th>
                                            <td>{{$client->address}}</td>
                                        </tr>
                                        {{-- <tr>
                                            <th>Facility</th>
                                            <td>{{$client->facility->name}}</td>
                                        </tr> --}}
                                        {{-- <tr>
                                            <th>Case Manager</th>
                                            <td>{{$client->caseManager->name}}</td>
                                        </tr> --}}
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
              </div>
              {{-- <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              </div> --}}
            </div>
          </div>
        </div>
        @endforeach
    </div>

    {{-- Add Facility Modal --}}
 <!-- Modal -->
            <div class="modal fade" id="add-client-form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">Register New Client</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <div class="row">
                       <div class="col-md-12">
                            <form action="{{route('add-client')}}" method="post">
                                @csrf
                                <div class="form-group row">
                                    <label for="inputEmail3" class="col-sm-3 col-form-label">Client Name</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{old('name')}}" required>
                                        @if ($errors->has('name'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('name') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                               <div class="form-group row">
                                    <label for="inputEmail3" class="col-sm-3 col-form-label">Client ID</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control{{ $errors->has('clientID') ? ' is-invalid' : '' }}" name="clientID" value="{{old('clientID')}}" required>
                                        @if ($errors->has('clientID'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('clientID') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inputEmail3" class="col-sm-3 col-form-label">Phone Number</label>
                                    <div class="col-sm-9">
                                        <input type="phone" class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}" name="phone" value="{{old('phone')}}" required>
                                        @if ($errors->has('phone'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('phone') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inputEmail3" class="col-sm-3 col-form-label">Phone Number OPC (Optional)</label>
                                    <div class="col-sm-9">
                                        <input type="phone" class="form-control{{ $errors->has('opc_phone') ? ' is-invalid' : '' }}" name="opc_phone" value="{{old('opc_phone')}}">
                                        @if ($errors->has('opc_phone'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('opc_phone') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inputEmail3" class="col-sm-3 col-form-label">Residential Address</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control{{ $errors->has('address') ? ' is-invalid' : '' }}" name="address" value="{{old('address')}}">
                                        @if ($errors->has('address'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('address') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                 <div class="form-group row">
                                    <div class="input-group mb-3 col-sm-12">
                                        <div class="input-group-prepend">
                                            <label class="input-group-text" for="inputGroupSelect01">Facility</label>
                                        </div>
                                        <select class="custom-select{{ $errors->has('facility') ? ' is-invalid' : '' }}" name="facility" id="inputGroupSelect01" disabled>
                                            <option value="{{$facility->id}}" selected>{{$facility->name}}</option>
                                        </select>
                                        @if ($errors->has('facility'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('facility') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="input-group mb-3 col-sm-12">
                                        <div class="input-group-prepend">
                                            <label class="input-group-text" for="inputGroupSelect01">Assign Case Manager</label>
                                        </div>
                                        <select class="custom-select{{ $errors->has('case_manager') ? ' is-invalid' : '' }} case_managers_select" name="case_manager" selected="{{old('case_manager')}}" id="inputGroupSelect01" required title="{{route('find_case_managers')}}">
                                            <option value="">Select case manager</option>
                                            @foreach($managers as $mg)
                                                <option value="{{$mg->id}}">{{$mg->name}}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('case_manager'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('case_manager') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="input-group mb-3 col-sm-12">
                                        <div class="input-group-prepend">
                                            <label class="input-group-text" for="inputGroupSelect01">Status</label>
                                        </div>
                                        <select class="custom-select{{ $errors->has('status') ? ' is-invalid' : '' }}" name="status" selected="{{old('status')}}" id="inputGroupSelect01" required>
                                            <option value=""> Choose...</option>
                                            <option value="Active">Active</option>
                                            <option value="Dead">Dead</option>
                                            <option value="Transferred Out">Transferred Out</option>
                                            <option value="Lost to Follow Up">Lost to Follow Up</option>
                                            <option value="Stop Treatment">Stop Treatment</option>
                                        </select>
                                        @if ($errors->has('status'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('status') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                
                                <div class="form-group row">
                                    <div class="col-sm-10">
                                        <button type="submit" class="btn  btn-primary reg-btn-case-mg">Add</button>
                                    </div>
                                </div>
                            </form>
                       </div>         
                  </div>
                </div>
              </div>
            </div>
            {{-- Add Facility Modal ends --}}
    @endsection
