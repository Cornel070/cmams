@extends('layouts.dashboard')
@section('content')
@section('sweet-alert-area')
    <script src="{{asset('assets/js/sweetalert2.js')}}" defer></script>
@endsection



<!-- [ stiped-table ] start -->
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Registered Case Managers</h5>
                        <span class="d-block m-t-5">There are a total of <b><code>{{$case_managers->count()}}</code></b> case manager(s) registered</span>
                        <button type="button" class="btn btn-info btn-sm add-btn" data-toggle="modal" data-target="#add-case-manager-form">
                            <i class="la la-plus-circle"></i> Add Case Manager</button>
                    </div>
                    <div class="card-body table-border-style">
                        <div class="table-responsive">

                            <table class="table table-striped" id="entry-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Case Manager Name</th>
                                        <th>Facility</th>
                                        <th>No. of Clients</th>
                                        <th>Avg Performance</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($case_managers as $case_mg)
                                    <tr id="de-{{$case_mg->id}}">
                                        <td>{{$case_mg->id}}</td>
                                        <td>{{$case_mg->name}}</td>
                                        <td>{{$case_mg->facility->name}}</td>
                                        <td>
                                            @if($case_mg->clients->count() > 0)
                                                <a href="{{route('view_clients_cm', $case_mg->id)}}" data-toggle="tooltip"
                                                    title="View clients assigned to {{$case_mg->name}}">
                                                    {{$case_mg->clients->count()}}
                                                </a>
                                            @else
                                                {{$case_mg->clients->count()}}
                                            @endif
                                        </td>
                                        <td>{{ cm_performance($case_mg) }}%</td>
                                        <td>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <button type="button" class="btn btn-info btn-sm" data-toggle="tooltip" title="Edit case manager" onclick="window.location.href='{{route('edit-case_mg',$case_mg->id)}}'"><i class="la la-edit"></i>
                                                    </button>
                                                </div>
                                                <div class="col-md-4">
                                                <span data-toggle="modal" data-target="#mg{{$case_mg->id}}">
                                                    <button type="button" class="btn btn-primary btn-sm" data-toggle="tooltip" data-placement="top" title="View case manager">
                                                        <i class="la la-eye"></i>
                                                    </button>
                                                </span>
                                                </div>
                                                <div class="col-md-4">
                                                    <button type="button" id="{{$case_mg->id}}" class="btn btn-danger btn-sm delete-btn-cm" data-toggle="tooltip" title="Delete case manager" ><i class="la la-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- View Facility Modal --}}
        @foreach($case_managers as $case_mg)
        <div class="modal fade" id="mg{{$case_mg->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h3 class="modal-title cm_view-title" id="exampleModalLabel">
                    {{-- <i class="la la-briefcase-medical"></i> --}}
                        <img class="img-radius case-mg-photo-view" src="{{asset('assets/images/uploads/'.$case_mg->profile_photo)}}" alt="User-Profile-Image">
                        <div class="user-details manager-name">
                            <div id="more-details">{{$case_mg->name}}</div>
                        </div>
                </h3>
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
                                            <th>Email Address</th>
                                            <td>{{ $case_mg->email }}</td>
                                        </tr>
                                        <tr>
                                            <th>Phone No.</th>
                                            <td>{{ $case_mg->phone }}</td>
                                        </tr>
                                        <tr>
                                            <th>Facility</th>
                                            <td>{{ $case_mg->facility->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>No. of Clients</th>
                                            <td>{{ $case_mg->clients->count() }}</td>
                                        </tr>
                                        <tr>
                                            <th>Avg. Performamce</th>
                                            <td>{{ cm_performance($case_mg) }}%</td>
                                        </tr>
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
            <div class="modal fade" id="add-case-manager-form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">Register New Case Manager</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <div class="row">
                       <div class="col-md-12">
                            <div class="col-md-12 options">
                                <div class="single-tab"><i class="la la-file-o"> Single Facilty</i></div>
                                <div class="bulk-tab"><i class="la la-files-o"></i> Bulk Upload</div>
                            </div>
                            <form action="{{route('add-case-manager')}}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="single-upload">
                                    <div class="back-arrow"><i class="la la-long-arrow-left"></i></div>
                                    <div class="form-group row">
                                        <label for="inputEmail3" class="col-sm-3 col-form-label">Full Name</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{old('name')}}" placeholder="surname firstname middlename">
                                            @if ($errors->has('name'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('name') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="inputEmail3" class="col-sm-3 col-form-label">Email Address</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{old('email')}}">
                                            @if ($errors->has('email'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('email') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="inputEmail3" class="col-sm-3 col-form-label">Phone Number</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}" name="phone" value="{{old('phone')}}">
                                            @if ($errors->has('phone'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('phone') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="inputEmail3" class="col-sm-3 col-form-label">Facility</label>
                                        <div class="col-sm-9">
                                            <select class="form-control{{ $errors->has('facility') ? ' is-invalid' : '' }} select-or-search" name="facility" value="{{old('facility')}}"  placeholder="Pick a facility">
                                                <option>...</option>
                                                @foreach($facilities as $fac)
                                                <option value="{{$fac->id}}">{{$fac->name}}</option>
                                                @endforeach
                                            </select>
                                                @if ($errors->has('facility'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('facility') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="input-group mb-3 col-sm-12">
                                            <div class="input-group-prepend">
                                                <label class="input-group-text">Profile Photo</label>
                                            </div>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input{{ $errors->has('profile_photo') ? ' is-invalid' : '' }}" id="inputGroupFile01" name="profile_photo" required="">
                                                 @if ($errors->has('profile_photo'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('profile_photo') }}</strong>
                                                </span>
                                                @endif
                                                <span class="custom-file-label" for="inputGroupFile01">Choose file</span>
                                            </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-10">
                                            <button type="submit" class="btn  btn-primary reg-btn">Add</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="bulk-upload">
                                    <div class="back-arrow"><i class="la la-long-arrow-left"></i></div>
                                        <div class="input-group mb-3 col-sm-12">
                                            <div class="input-group-prepend">
                                                <label class="input-group-text">Upload CSV</label>
                                            </div>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input{{ $errors->has('bulk-facility') ? ' is-invalid' : '' }}" id="inputGroupFile01" name="bulk-facility">
                                                 @if ($errors->has('bulk-facility'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('bulk-facility') }}</strong>
                                                </span>
                                                @endif
                                                <span class="custom-file-label" for="inputGroupFile01">Choose file</span>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-sm-10">
                                                <button type="submit" class="btn  btn-primary reg-btn">Add</button>
                                            </div>
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
