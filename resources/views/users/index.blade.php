@extends('layouts.master')

@section('content')

    @push('css')
        <link rel="stylesheet" type="text/css" href="../../../app-assets/css/pages/data-list-view.css">
    @endpush
    <section id="data-list-view" class="data-list-view-header">
        <div class="action-btns d-none">
            <div class="btn-dropdown mr-1 mb-1">
                <div class="btn-group dropdown actions-dropodown">
                    <button type="button" class="btn btn-white px-1 py-1 dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Actions
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="#"><i class="feather icon-trash"></i>Delete</a>
                        <a class="dropdown-item" id="export-user" href="#"><i class="feather icon-archive"></i>Export</a>
                        <a class="dropdown-item" href="{{route('users.print')}}"><i class="feather icon-file"></i>Print Pdf</a>
                        <a class="dropdown-item" href="#"><i class="feather icon-save"></i>Another Action</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="media-body mt-50">
            <h4 class="media-heading">{{$user->name ?? ''}}</h4>
            @if (session('status'))
                <div class="alert alert-success alert-dismissible mb-2" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <p class="mb-0">
                        {{ session('status') }}
                    </p>
                </div>
            @endif

        </div>
        <!-- DataTable starts -->
        <section id="basic-datatable">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Users</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body card-dashboard">
                                <div class="table-responsive">
                                    <table class="table zero-configuration" id="users-table">
                                        <thead>
                                        <tr>
                                            <th><input type="checkbox"></th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Create AT</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($users as $user)
                                            <tr>
                                                <th><input value="{{$user->id}}" class="select-param" type="checkbox"></th>
                                                <th>{{$user->name}}</th>
                                                <th>{{$user->email}}</th>
                                                <th>{{$user->created_at}}</th>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- DataTable ends -->
        <div class="add-new-data-sidebar">
            <div class="overlay-bg"></div>
            <div class="add-new-data">
                <div class="div mt-2 px-2 d-flex new-data-title justify-content-between">
                    <div>
                        <h4 class="text-uppercase">Import</h4>
                    </div>
                    <div class="hide-data-sidebar">
                        <i class="feather icon-x"></i>
                    </div>
                </div>
                <div class="data-items pb-3">
                    <div class="data-fields px-2 mt-3">
                        <div class="row">
                            <div class="col-sm-12 data-field-col data-list-upload">
                                <form method="POST"  id="dataListUpload" action="{{ route('users.import')}}"  enctype="multipart/form-data" autocomplete="off">
                                    @csrf
                                    @method('post')
                                    <input class="dz-message" type="file" name="import" id="import-user" required>
                                    <a class="dz-message" href="{{asset('app-assets/sample-data/import-user.xlsx')}}">Sample DATA</a>
                                    <div class="add-data-footer d-flex justify-content-around px-3 mt-2">
                                        <div class="add-data-btn">
                                            <button type="submit" class="btn btn-primary">Import</button>
                                        </div>
                                        <div class="cancel-data-btn">
                                            <button type="button"  class="btn btn-outline-danger">Cancel</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @push('js')
        <script>

            $(document).ready(function(){

                var postURL = "{{route('users.export')}}";
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $('#export-user').click(function(){
                    var userIds = [];
                    $("input:checkbox[name=userIds]:checked").each(function(){
                        userIds.push($(this).val());
                    });
                    $.ajax({
                        url:postURL,
                        method:"POST",
                        data: {userIds:userIds},
                        type:'json',
                        xhrFields:{
                            responseType: 'blob'
                        },
                        success:function(data)
                        {
                            var link = document.createElement('a');
                            link.href = window.URL.createObjectURL(data);
                            link.download = 'users.xlsx';
                            link.click();
                        }
                    });
                });
            });
        </script>
    @endpush

@endsection
