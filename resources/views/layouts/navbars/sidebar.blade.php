<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="shadow-bottom"></div>
    <div class="main-menu-content">
        <div class="main-menu-content">
            <div class="navbar-header">
                <ul class="nav navbar-nav flex-row">
                    <li class="nav-item mr-auto">
                        <a class="navbar-brand" href="{{route('home')}}">
                            <div class="logo"></div>
                            <h2 class="brand-text mb-0"></h2>
                        </a>
                    </li>
                    <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse"><i
                                class="feather icon-x d-block d-xl-none font-medium-4 primary toggle-icon"></i><i
                                class="toggle-icon feather icon-disc font-medium-4 d-none d-xl-block collapse-toggle-icon primary"
                                data-ticon="icon-disc"></i></a></li>
                </ul>
            </div>
            <div class="shadow-bottom"></div>
            <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
                <li class="nav-item nav-item-parent"><a href="{{route('home')}}"><i class="fa fa-truck"></i><span
                            class="menu-title">Theo dõi khối lượng</span></a></li>
                <li class=" nav-item"><a><i class="fa fa-list-alt "></i><span
                            class="menu-title">{{__('Danh mục khối lượng')}}</span></a>
                    <ul class="menu-content">
                        <li><a href="{{route('customers')}}"><i class="feather icon-circle"></i><span
                                    class="menu-item">{{__('Khách hàng')}}</span></a></li>
                        <li><a href="{{route('suppliers')}}"><i class="feather icon-circle"></i><span
                                    class="menu-item">{{__('Nhà cung cấp')}}</span></a></li>
                        <li><a href="{{route('constructions')}}"><i class="feather icon-circle"></i><span
                                    class="menu-item">{{__('Công trình')}}</span></a></li>
                        <li><a href="{{route('areas')}}"><i class="feather icon-circle"></i><span
                                    class="menu-item">{{__('Khu vực')}}</span></a></li>
                        <li><a href="{{route('stations')}}"><i class="feather icon-circle"></i><span
                                    class="menu-item">{{__('Trạm')}}</span></a></li>
                        <li><a href="{{route('vehicles')}}"><i class="feather icon-circle"></i><span
                                    class="menu-item">{{__('Phương tiện đổ')}}</span></a></li>
                        <li><a href="{{route('slumps')}}"><i class="feather icon-circle"></i><span
                                    class="menu-item">{{__('Độ sụt')}}</span></a></li>
                        <li><a href="{{route('sampleages')}}"><i class="feather icon-circle"></i><span
                                    class="menu-item">{{__('Tuổi mẫu')}}</span></a></li>
                        <li><a href="{{route('concrete_grades')}}"><i class="feather icon-circle"></i><span
                                    class="menu-item">{{__('Mác bê tông')}}</span></a></li>
                    </ul>
                </li>
                <li class=" nav-item"><a><i class="feather icon-calendar"></i><span
                            class="menu-title">{{__('Phiếu đánh giá')}}</span></a>
                    <ul class="menu-content">
                        @php
                        $surveys = surveyByPermission();
                        @endphp
                        @foreach($surveys as $key => $survey)
                        @if($key == 0)
                        <li><a href="{{route('surveyDetails.addSurvey',['survey'=>$survey->id])}}"><i
                                    class="feather icon-circle"></i><span
                                    class="menu-item">{{__('Phiếu số '.($key+1).' (Công trình)')}}</span></a></li>
                        @elseif($key == 1)
                        <li><a href="{{route('surveyDetails.addSurvey',['survey'=>$survey->id])}}"><i
                                    class="feather icon-circle"></i><span
                                    class="menu-item">{{__('Phiếu số '.($key+1).' (Văn phòng)')}}</span></a></li>
                        @endif
                        @endforeach
                        <li><a
                                href="{{route('surveyRecords.show',['survey'=>'bd70738d-04a4-4263-90ff-043db1f8f6bc'])}}"><i
                                    class="feather icon-file-text"></i><span
                                    class="menu-item">{{__('Báo cáo')}}</span></a></li>
                        <li><a
                                href="{{route('users.make-link')}}"><i
                                    class="feather icon-file-text"></i><span
                                    class="menu-item">{{__('Tạo link khải sát')}}</span></a></li>
                    </ul>
                </li>
                <li class=" nav-item"><a><i class="fa fa-calendar-o"></i><span
                            class="menu-title">{{__('Hợp đồng')}}</span></a>
                    <ul class="menu-content">
                        <li><a href="{{route('contracts')}}"><i class="feather icon-circle"></i><span
                                    class="menu-item">{{__('Theo dõi hợp đồng')}}</span></a></li>
                        <li><a href="{{route('payment_conditions')}}"><i class="feather icon-circle"></i><span
                                    class="menu-item">{{__('Điều kiện thanh toán')}}</span></a></li>
                    </ul>
                </li>
                <li class="nav-item"><a><i class="fa fa-calculator"></i><span
                            class="menu-title">{{__('D.mục q.lý công nợ')}}</span></a>
                    <ul class="menu-content">
                        <li><a href="{{route('organizations')}}"><i class="feather icon-circle"></i><span
                                    class="menu-item">{{__('Thông tin công ty')}}</span></a></li>
                        <li><a href="{{route('transaction_types')}}"><i class="feather icon-circle"></i><span
                                    class="menu-item">{{__('Khoản mục chi phí')}}</span></a></li>
                        <li><a href="{{route('bank_accounts')}}"><i class="feather icon-circle"></i><span
                                    class="menu-item">{{__('Tài khoản ngân hàng')}}</span></a></li>
                        <li><a href="{{route('gl_accounts')}}"><i class="feather icon-circle"></i><span
                                    class="menu-item">{{__('Tài khoản kế toán')}}</span></a></li>
                        <li><a href="{{route('payment_methods')}}"><i class="feather icon-circle"></i><span
                                    class="menu-item">{{__('Phương thức thanh toán')}}</span></a></li>
                        <li><a href="{{route('transaction_entries')}}"><i class="feather icon-circle"></i><span
                                    class="menu-item">{{__('Định khoản phiếu KL')}}</span></a></li>
                    </ul>
                </li>
                <li class=" nav-item"><a><i class="fa fa-print"></i><span class="menu-title"
                            data-i18n="Menu Levels">{{__('Dữ liệu kế toán')}}</span></a>
                    <ul class="menu-content">
                        <li><a href="{{route('receipts')}}"><i class="feather icon-circle"></i><span
                                    class="menu-item">Phiếu
                                    thu</span></a>
                        </li>
                        <li><a href="{{route('payments')}}"><i class="feather icon-circle"></i><span
                                    class="menu-item">Phiếu
                                    chi</span></a>
                        </li>
                        <li><a href="{{route('debits')}}"><i class="feather icon-circle"></i><span class="menu-item">Báo
                                    nợ</span></a>
                        </li>
                        <li><a href="{{route('alerts')}}"><i class="feather icon-circle"></i><span class="menu-item">Báo
                                    có</span></a>
                        </li>
                    </ul>
                </li>
                {{-- <li class=" nav-item"><a><i class="feather icon-file-text"></i><span class="menu-title"
                    data-i18n="Menu Levels">{{__('Báo cáo công nợ')}}</span></span></a>
                    <ul class="menu-content">
                        <li><a href="{{route('overviews')}}"><i class="feather icon-circle"></i><span class="menu-item">Tổng quan</span></a></li>
                        <li><a href="{{route('details')}}"><i class="feather icon-circle"></i><span class="menu-item">Chi tiết</span></a></li>
                        <li><a href="{{route('turnovers')}}"><i class="feather icon-circle"></i><span class="menu-item">Doanh thu</span></a></li>
                        <li><a href="{{route('overdues')}}"><i class="feather icon-circle"></i><span class="menu-item">Nợ quá hạn</span></a></li>
                        <li><a href="{{route('debt-structures')}}"><i class="feather icon-circle"></i><span class="menu-item">Cơ cấu nợ</span></a></li>
                        <li><a href="{{route('debt-collections')}}"><i class="feather icon-circle"></i><span class="menu-item">Thu nợ</span></a></li>
                        <li><a href="{{route('detail-debt-collections')}}"><i class="feather icon-circle"></i><span class="menu-item">Chi tiết thu nợ</span></a></li>
                    </ul>
                </li> --}}
                @role('admin')
                <li class="nav-item nav-item-parent"><a href="{{route('users')}}"><i class="feather icon-user"></i><span
                            class="menu-title">Quản lý người dùng</span></a></li>
                @endrole
            </ul>
        </div>
    </div>
</div>