<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>DUFAGO</title>
    <link rel="apple-touch-icon" href="/app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="/app-assets/images/ico/ICON.png">
    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" href="/css/jquery.treegrid.css">
    @include('layouts.css.master')
    @stack('css')
    <!-- END: Vendor CSS-->
    
    
</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="{{ $class ?? 'vertical-layout content-left-sidebar navbar-floating footer-static vertical-menu-modern'}}"
    data-open="click" data-menu="vertical-menu-modern" data-col="content-left-sidebar" >
    @auth()
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <!-- BEGIN: Header-->
    @include('layouts.navbars.navbar')
    <!-- END: Header-->

    <!-- BEGIN: Main Menu-->
    @include('layouts.navbars.sidebar')
    <!-- END: Main Menu-->
    @endauth
    <!-- BEGIN: Content-->
    <div class="app-content content" id="app-content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                @yield('content')
            </div>
        </div>
    </div>
    <!-- END: Content-->

    
    @include('layouts.js.master')
    @stack('js')
    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>
</body>

</html>