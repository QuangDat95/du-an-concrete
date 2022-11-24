@extends('layouts.master',['class' => 'vertical-layout vertical-menu-modern 1-column navbar-floating footer-static
bg-full-screen-image blank-page blank-page pace-done'])

@section('content')
<style>
    a, a:hover{
  color:#333
}
.input-group-addon {
    padding-top: 0.5rem;
    padding-left: 0.9rem;
    padding-right: 0.9rem;
    border: 1px solid #d9d9d9;
    border-left: none;
    border-radius: 0px 4px 4px 0px;
}
</style>
<section class="row flexbox-container">
    <div class="col-xl-8 col-11 d-flex justify-content-center">
        <div class="card bg-authentication rounded-0 mb-0">
            <div class="row m-2">
                <div class="col-lg-6 d-lg-block d-none text-center align-self-center px-1 py-0">
                    <img src="../../../app-assets/images/pages/login.png" alt="branding logo">
                </div>
                <div class="col-lg-6 col-12 p-0">
                    <div class="card rounded-0 mb-0 px-2">
                        <div class="card-header pb-1">
                            <div class="card-title">
                                <h4 class="mb-0">Đăng nhập</h4>
                            </div>
                        </div>
                        <p class="px-2">Đăng nhập vào tài khoản của bạn</p>
                        <div class="card-content">
                            <div class="card-body pt-1">
                                <form method="POST" action="{{route('auth.login')}}">
                                    @csrf
                                    <fieldset class="form-label-group form-group position-relative has-icon-left">
                                        <input class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                            placeholder="{{ __('Email') }}" type="email" name="email" required
                                            autocomplete="off" autofocus>
                                        <div class="form-control-position">
                                            <i class="feather icon-user"></i>
                                        </div>
                                        <label for="user-name">Email</label>
                                    </fieldset>

                                    <fieldset class="form-label-group position-relative has-icon-left">
                                        <div class="input-group" id="show_hide_password">
                                            <input
                                                class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                                                name="password" placeholder="{{ __('Password') }}" type="password"
                                                autocomplete="off" required>
                                            <div class="form-control-position">
                                                <i class="feather icon-lock"></i>
                                            </div>
                                            <div class="input-group-addon">
                                                <a><i id="change-eye" class="fa fa-eye-slash" aria-hidden="true"></i></a>
                                            </div>
                                        </div>
                                        <label for="user-password">Mật khẩu</label>
                                    </fieldset>
                                    <button type="submit" class="btn btn-primary float-right btn-inline">Đăng
                                        nhập</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@push('js')
<script src="/js/showpass.js"></script>
@endpush
@endsection