@extends('layouts.master' , ['class'=> 'vertical-layout 2-columns navbar-floating footer-static pace-done
vertical-overlay-menu menu-hide'])
@section('content')
<section class="users-edit">
    <div class="card">
        <div class="card-content">
            <div class="card-body">
                <ul class="nav nav-tabs mb-3" role="tablist" id="user-tab">
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center active" id="account-tab" data-toggle="tab"
                            href="#account" aria-controls="account" role="tab" aria-selected="true">
                            <i class="feather icon-user mr-25"></i><span class="d-none d-sm-block">Account</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center" id="password-tab" data-toggle="tab"
                            href="#password" aria-controls="password" role="tab" aria-selected="false">
                            <i class="feather icon-lock mr-25"></i><span class="d-none d-sm-block">Password</span>
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="account" aria-labelledby="account-tab" role="tabpanel">
                        <!-- users edit media object start -->
                        @if ($errors->has('image'))
                        <div class="alert alert-warning alert-dismissible mb-2" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                            <p class="mb-0">
                                {{ $errors->first('image') }}
                            </p>
                        </div>
                        @endif
                        <div class="media mb-2">
                            <a class="mr-2 my-25" href="#">
                                @if($user->image ?? '')
                                <img id="users-avatar" src="{{Storage::url(Auth::user()->image)}}"
                                    alt="users avatar" class="users-avatar-shadow rounded" height="90" width="90">
                                @else
                                <img id="users-avatar" src="../../../app-assets/images/profile/user-uploads/User.png"
                                    alt="users avatar" class="users-avatar-shadow rounded" height="90" width="90">
                                @endif
                            </a>
                            <div class="media-body mt-50">
                                <h4 class="media-heading">{{$user->name ?? ''}}</h4>
                                <div class="col-12 d-flex mt-1 px-0">
                                    <label class="btn btn-primary d-none d-sm-block mr-75" for="account-upload">Upload
                                        new photo</label>
                                    <button type="button"
                                        class="btn btn-sm btn-outline-warning ml-50 click_submit">save</button>
                                    <a href="#" class="btn btn-primary d-block d-sm-none mr-75"><i
                                            class="feather icon-edit-1"></i></a>
                                    <a href="#" class="btn btn-outline-danger d-block d-sm-none"><i
                                            class="feather icon-trash-2"></i></a>
                                </div>
                                <p class="text-muted ml-75 mt-50"><small>Allowed JPG, GIF or PNG. Max size of
                                        800kB</small></p>

                            </div>

                        </div>
                        @if ($errors->has('old_password') OR $errors->has('password') OR
                        $errors->has('password_confirmation'))
                        <div class="alert alert-warning alert-dismissible mb-2" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                            <p class="mb-0">
                                {{$errors->first() }}
                            </p>
                        </div>
                        @endif
                        <!-- users edit media object ends -->
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
                        <!-- users edit account form start -->
                        <!-- action="{{ route('users',['user' => old('id', $user->id ?? '')]) }}" -->
                        <form id="upload-image-form" method="POST" enctype="multipart/form-data" autocomplete="off">
                            @csrf
                            <!-- <input type="file" name="file" id="account-upload" hidden onchange="loadFile(event)" value="{{ old('image', $user->image ?? '') }}"> -->
                            <input type="file" name="file" id="account-upload" hidden onchange="readURL(this)"
                                value="{{ old('image', $user->image ?? '') }}">
                        </form>
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <div class="controls">
                                        <label for="account-name">Account</label>
                                        <input type="text" class="form-control" id="account-name" name="name"
                                            placeholder="Email" value="{{ old('name', $user->name ?? '') }}" disabled
                                            required>
                                        @if ($errors->has('name'))
                                        <div class="alert alert-warning alert-dismissible mb-2" role="alert">
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">×</span>
                                            </button>
                                            <p class="mb-0">
                                                {{ $errors->first('name') }}
                                            </p>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                <!-- <div class="form-group">
                                            <div class="controls">
                                                <label for="account-name">First Name</label>
                                                <input type="text" class="form-control" id="account-name" name="first_name" placeholder="First Name" value="{{ old('first_name', $user->person->first_name ?? '') }}" required>
                                                @if ($errors->has('first_name'))
                                                    <div class="alert alert-warning alert-dismissible mb-2" role="alert">
                                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                            <span aria-hidden="true">×</span>
                                                        </button>
                                                        <p class="mb-0">
                                                            {{ $errors->first('first_name') }}
                                                        </p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div> -->
                                <!-- <div class="form-group">
                                            <div class="controls">
                                                <label for="account-name">Middle Name</label>
                                                <input type="text" class="form-control" id="account-name" name="middle_name" placeholder="Middle Name" value="{{ old('middle_name', $user->person->middle_name ?? '') }}" required>
                                                @if ($errors->has('middle_name'))
                                                    <div class="alert alert-warning alert-dismissible mb-2" role="alert">
                                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                            <span aria-hidden="true">×</span>
                                                        </button>
                                                        <p class="mb-0">
                                                            {{ $errors->first('middle_name') }}
                                                        </p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div> -->
                                <!-- <div class="form-group">
                                            <div class="controls">
                                                <label for="account-name">Gender</label>
                                                <select class="form-control" id="select-gender"  name="gender">
                                                    <option value="1">Nam</option>
                                                    <option @if(old('gender', $user->person->gender ?? '') == 2) selected @endif value="2">Nữ</option>
                                                </select>
                                                @if ($errors->has('gender'))
                                                    <div class="alert alert-warning alert-dismissible mb-2" role="alert">
                                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                            <span aria-hidden="true">×</span>
                                                        </button>
                                                        <p class="mb-0">
                                                            {{ $errors->first('gender') }}
                                                        </p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div> -->
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <div class="controls">
                                        <label for="account-name">E-mail</label>
                                        <input type="text" class="form-control" id="account-name" name="email"
                                            placeholder="Email" value="{{ old('email', $user->email ?? '') }}" disabled
                                            required>
                                        @if ($errors->has('email'))
                                        <div class="alert alert-warning alert-dismissible mb-2" role="alert">
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">×</span>
                                            </button>
                                            <p class="mb-0">
                                                {{ $errors->first('email') }}
                                            </p>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                <!-- <div class="form-group">
                                            <div class="controls">
                                                <label for="account-name">Last Name</label>
                                                <input type="text" class="form-control" id="account-name" name="last_name" placeholder="Last Name" value="{{ old('last_name', $user->person->last_name ?? '') }}" required>
                                                @if ($errors->has('last_name'))
                                                    <div class="alert alert-warning alert-dismissible mb-2" role="alert">
                                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                            <span aria-hidden="true">×</span>
                                                        </button>
                                                        <p class="mb-0">
                                                            {{ $errors->first('last_name') }}
                                                        </p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label for="account-name">Birthday</label>
                                                <fieldset class="form-group position-relative">
                                                    <input type="text" class="form-control datepicker" name="birthday" placeholder="birthday" value="{{ old('birthday', $user->person->birthday ?? '') }}" required>
                                                    <div class="form-control-position datepicker-icon">
                                                        <i class="fa fa-calendar"></i>
                                                    </div>
                                                </fieldset>
                                                @if ($errors->has('birthday'))
                                                    <div class="alert alert-warning alert-dismissible mb-2" role="alert">
                                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                            <span aria-hidden="true">×</span>
                                                        </button>
                                                        <p class="mb-0">
                                                            {{ $errors->first('birthday') }}
                                                        </p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div> -->
                                <div class="form-group">
                                    <div class="controls">
                                        <label for="account-name"></label>

                                    </div>
                                </div>
                                <!-- <div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
                                    <button type="submit" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1">Save
                                        Changes</button>
                                    <button type="reset" class="btn btn-outline-warning">Reset</button>
                                </div> -->
                            </div>
                        </div>
                        {{--                        @include('users.permission')--}}

                        <!-- users edit account form ends -->
                    </div>
                    <div class="tab-pane" id="password" aria-labelledby="password-tab" role="tabpanel">
                        <!-- users edit Info form start -->
                        <form method="POST"
                            action="{{ route('users.password',['user' => old('id', $user->id ?? '')]) }}"
                            autocomplete="off">
                            @csrf
                            @method('put')
                            <input type="hidden" name="tab" value="password">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <div class="controls">
                                            <label for="account-old-password">Old Password</label>
                                            <input type="password" class="form-control" id="account-old-password"
                                                required placeholder="Old Password" name="old_password">
                                            @if ($errors->has('old_password'))
                                            <div class="alert alert-warning alert-dismissible mb-2" role="alert">
                                                <button type="button" class="close" data-dismiss="alert"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">×</span>
                                                </button>
                                                <p class="mb-0">
                                                    {{ $errors->first('old_password') }}
                                                </p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <div class="controls">
                                            <label for="account-new-password">New Password</label>
                                            <input type="password" name="password" id="account-new-password"
                                                class="form-control" placeholder="New Password" required
                                                data-validation-required-message="The password field is required"
                                                minlength="6">
                                            @if ($errors->has('password'))
                                            <div class="alert alert-warning alert-dismissible mb-2" role="alert">
                                                <button type="button" class="close" data-dismiss="alert"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">×</span>
                                                </button>
                                                <p class="mb-0">
                                                    {{ $errors->first('password') }}
                                                </p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <div class="controls">
                                            <label for="account-retype-new-password">Confirmation Password</label>
                                            <input type="password" name="password_confirmation" class="form-control"
                                                required id="account-retype-new-password"
                                                data-validation-match-match="password" placeholder="New Password"
                                                data-validation-required-message="The Confirm password field is required"
                                                minlength="6">
                                            @if ($errors->has('password_confirmation'))
                                            <div class="alert alert-warning alert-dismissible mb-2" role="alert">
                                                <button type="button" class="close" data-dismiss="alert"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">×</span>
                                                </button>
                                                <p class="mb-0">
                                                    {{ $errors->first('password_confirmation') }}
                                                </p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 d-flex flex-sm-row flex-column justify-content-end">
                                    <button type="submit" class="btn btn-primary mr-sm-1 mb-1 mb-sm-0">Save
                                        changes</button>
                                    <button type="reset" class="btn btn-outline-warning">Cancel</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@push('js')
<!-- BEGIN: Page Vendor JS-->
<script src="../../../app-assets/vendors/js/extensions/sweetalert2.all.min.js"></script>
<script src="../../../app-assets/vendors/js/extensions/polyfill.min.js"></script>
<!-- END: Page Vendor JS-->
<script src="../../../app-assets/js/scripts/extensions/sweet-alerts.js"></script>
<script src="{{ mix('dashboards/js/reorder-form.js') }}"></script>
<script>
$('a[data-toggle="tab"]').on('show.bs.tab', function(e) {

    localStorage.setItem('activeTab', $(e.target).attr('href'));

});

var activeTab = localStorage.getItem('activeTab');

if (activeTab) {

    $('#user-tab a[href="' + activeTab + '"]').tab('show');

}

var loadFile = function(event) {
    var output = document.getElementById('users-avatar');
    output.src = URL.createObjectURL(event.target.files[0]);
};
</script>
@endpush