<nav class="header-navbar navbar-expand-lg navbar navbar-with-menu floating-nav navbar-light navbar-shadow"
    id="sticky-custom">
    <div class="navbar-wrapper">
        <div class="navbar-container content">
            <div class="navbar-collapse" id="navbar-mobile">
                <div class="mr-auto float-center bookmark-wrapper d-flex align-items-center">
                    <ul class="nav navbar-nav">
                        <div id="navbar-title-content" class="text-center">
                            @if(isset($surveyTitle))
                            <h3 class="">{{$surveyTitle}}</h3>
                            @elseif(isset($titleListSurveyRecords))
                            <h3>{{$titleListSurveyRecords}}</h3>
                            @endif
                        </div>
                        <li class="nav-item mobile-menu d-xl-none mr-auto"><a
                                class="nav-link nav-menu-main menu-toggle hidden-xs is-active" href="#"><i
                                    class="ficon feather icon-menu"></i></a></li>
                    </ul>
                </div>
                <ul class="nav navbar-nav float-right">
                    <li class="dropdown dropdown-user nav-item">
                        <a class="dropdown-toggle nav-link dropdown-user-link" href="#" data-toggle="dropdown">
                            <div class="user-nav d-sm-flex d-none">
                                <span class="user-name text-bold-600">@if(Auth::check())
                                    {{Auth::user()->hovaten}}
                                    @else
                                    {{'underfined'}}
                                    @endif</span>
                                <span class="user-status">
                                    @if(auth()->user()->phongban != null)
                                    {{auth()->user()->phongban}}
                                    @else
                                    không có chức vụ
                                    @endif
                                </span>
                            </div>
                            <span id="account-img-reset">
                                @php
                                if(Auth::user()->image==""){
                                $imageProfileUser = 'default-image.png';
                                }
                                else{
                                $imageProfileUser = Auth::user()->image;
                                }
                                @endphp
                                <img class="round" id="img-avatar-employee"
                                src="{{asset('surveys/images/user_images/'.$imageProfileUser)}}" alt="avatar" height="40"
                                width="40">
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="{{route('users.edit',['user'=> auth()->user()->id])}}">
                                <i class="feather icon-user"></i>Edit Profile</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{route('logout')}}" onclick="event.preventDefault();
                                                                 document.getElementById('logout-form').submit();">
                                <i class="feather icon-power"></i>Đăng xuất</a>
                        </div>

                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>