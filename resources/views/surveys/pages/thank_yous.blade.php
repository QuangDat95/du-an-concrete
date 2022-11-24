@extends('layouts.master')
@section('content')
<style>
    body{
        background: url(/app-assets/images/pages/vuexy-login-bg.jpg) center;
        background-size: cover;
    }
    .navbar-floating .header-navbar-shadow{
    display: block !important;
    padding-top: 2.2rem !important;
    background: none;
    background-repeat: repeat !important;
    width: 100% !important;
    height: 102px !important;
    position: fixed !important;
    top: 0 !important;
    z-index: 11 !important;
}
</style>
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <section class="row flexbox-container">
                    <div class="col-xl-7 col-md-8 col-12 d-flex justify-content-center">
                        <div class="card auth-card bg-transparent shadow-none rounded-0 mb-0 w-100">
                            <div class="card-content">
                                <div class="card-body text-center">
                                    <img src="{{asset('image/thank_you.png')}}" class="img-fluid align-self-center" alt="thank you" width="100" height="100">
                                    <h1 class="font-large-2 mt-1 mb-0">Khảo sát thành công!</h1>
                                    <h3 class="p-3">
                                        Cảm ơn bạn đã thực hiện khảo sát này.
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection
