<html>

<head>
    <title>Phiếu chi</title>
    <style>
    body {
        margin: 0;
        background-color: white;
    }

    * {
        box-sizing: border-box;
        -moz-box-sizing: border-box;
    }

    .main-page {
        width: 21cm;
        min-height: 29.7cm;
        margin: 1cm auto;
        border: 1px solid white;
        border-radius: 5px;
        background: white;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    }

    .sub-page {
        padding: 1cm;
        height: 297mm;
    }

    @page {
        size: A4;
        margin: 0;
    }

    @media print {
        .main-page {
            margin: 0;
            padding: 0;
            border: initial;
            border-radius: initial;
            width: initial;
            min-height: initial;
            box-shadow: initial;
            background: initial;
            page-break-after: always;
        }
    }
    </style>
</head>

<body onload="window.print()">
    <div class="main-page">
        <div class="sub-page">
            <div style="position:absolute">
                <span style="position:relative;top:-20px">
                    <h4>Công ty TNHH Một Thành Viên bê tông DUFAGO</h4>
                </span>
            </div>
            <div style="position:absolute">
                <span style="position:relative;right:-563px;top:-20px">
                    <h4>Mẫu số: 02 - TT</h4>
                </span>
            </div>
            <div style="position:absolute">
                <span style="position: relative;font-size:14px;top:20px">233 Điện Biên
                    Phủ, Phường Hòa Khê, Quận Thanh Khê, Đà Nẵng, Việt Nam</span>
            </div>
            <div style="position:absolute">
                <span style="position:relative;font-size:14px;right:-502px;top:20px">Ban hành
                    theo TT số 200/2014/TT-BTC</span>
            </div>
            <div style="position: absolute">
                <span style="position:relative;right:-516px;top:38px;font-size:14px">Ngày 22/12/2014 của Bộ Tài
                    chính</span>
            </div>
            <div style="position:absolute">
                <h2 style="position:relative;right:-289px;top:86px">PHIẾU CHI</h2>
            </div>
            <div style="position:absolute">
                <span style="position: relative;top:120px;right:-262px">
                    <?php
				$paymentdate = explode('-',$payment->payment_date);
				?>
                    <h4>Ngày <?= $paymentdate[0] ?> tháng <?= $paymentdate[1] ?> năm <?= $paymentdate[2] ?></h4>
                </span>
            </div>
            <div style="position:absolute">
                <table class="table" style="width:260px;font-size:14px;position:relative;right:-465px;top:98px">
                    <thead>
                        <tr>
                            <th colspan="2"><span style="float:right">Số CT:</span></th>
                            <th><span style="float:left">{{$payment->code}}</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payment_item as $key => $value)
                        @if(isset($value->debitAccount->account_code))
                        @if($key == 0)
                        <tr>
                            <td><span style="float:right">TK Nợ:</span></td>
                            <td><span style="float:right">{{$value->debitAccount->account_code}}</span></td>
                            <td><span style="float:right">{{str_replace(',','.',number_format($value->amount))}}</span>
                            </td>
                        </tr>
                        @else
                        <tr>
                            <td><span style="float:right"></span></td>
                            <td><span style="float:right">{{$value->debitAccount->account_code}}</span></td>
                            <td><span style="float:right">{{str_replace(',','.',number_format($value->amount))}}</span>
                            </td>
                        </tr>
                        @endif
                        @endif
                        @endforeach
                        <tr>
                            <td><span style="float:right">TK Có:</span></td>
                            <td><span style="float:right">{{$creditAccount}}</span></td>
                            <td><span style="float:right">{{str_replace(',','.',number_format($sum[0]->sum))}}</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div style="position:absolute">
                <table class="table" style="font-size:14px;width:154mm;position:relative;top:171px">
                    <thead>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="width:72px">Họ và tên:</td>
                            <td>
                                {{$payment->partyable_name}}
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align:top;width:72px">
                                Địa chỉ:
                            </td>
                            <td>
                                <div style="width:420px">{{$address}}</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="width:72px">Lý do:</td>
                            <td>
                                <div>{{$payment->description}}</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div style="position:absolute">
                <table class="table" style="font-size:14px;width:131mm;position:relative;top:245px">
                    <thead>
                        <tr>
                            <td style="width:72px">Số tiền:</td>
                            <th colspan="4">
                                <div style="float:left">{{str_replace(',','.',number_format($payment->amount))}} đồng
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="vertical-align:top;width:72px">Bằng chữ:</td>
                            <th colspan="4">
                                <div style="float:left">
                                {{ucfirst(convert_number_to_words($payment->amount))}} đồng
                                </div>
                            </th>
                        </tr>
                        <tr>
                            <td style="width:72px">Kèm theo:</td>
                            <td>_______________________________________ chứng từ gốc</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div style="position:absolute">
                <span style="position:relative;top:330px">Đã nhận đủ số
                    tiền:__________________________________________________________________________</span><br>
                <span
                    style="position:relative;top:341px;left:4px">_________________________________________________________________________________________</span>
            </div>
            <div>
                <span
                    style="float:right;position:relative;top:401px;right:19px">Ngày.......tháng.......năm.........</span>
            </div>
            <div style="position:absolute">
                <table class="table" style="font-size:14px;width:201mm;position:relative;top:435px;left:-33px">
                    <thead>
                        <tr>
                            <th>Thủ trưởng đơn</th>
                            <th>Kế toán trưởng</th>
                            <th>Kế toán thanh toán</th>
                            <th>Người nhận tiền</th>
                            <th>Thủ quỹ</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div style="position:absolute">
                <span
                    style="position:relative;top:580px">________________________________________________________________________________________</span>
            </div>
        </div>
    </div>
</body>

</html>