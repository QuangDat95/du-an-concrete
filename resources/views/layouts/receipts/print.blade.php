<html>

<head>
    <title>Phiếu thu</title>
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
                    <h4>Mẫu số: 01 - TT</h4>
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
                <h2 style="position:relative;right:-289px;top:86px">PHIẾU THU</h2>
            </div>
            <div style="position:absolute">
                <span style="position: relative;top:120px;right:-262px">
				<?php
				$paymentdate = explode('-',$receipt->payment_date);
				?>
                    <h4>Ngày <?= $paymentdate[0] ?> tháng <?= $paymentdate[1] ?> năm <?= $paymentdate[2] ?></h4>
                </span>
            </div>
            <div style="position:absolute">
                <table class="table" style="width:260px;font-size:14px;position:relative;right:-465px;top:98px">
                    <thead>
                        <tr>
                            <th colspan="2"><span style="float:right">Số CT:</span></th>
                            <th><span style="float:left">{{$receipt->code}}</span></th>
                        </tr>
                    </thead>
                    <tbody>
						@foreach($payment_item as $key => $value)
						@if(isset($value->debitAccount->account_code))
						@if($key == 0)
                        <tr>
                            <td><span style="float:right">TK Nợ:</span></td>
                            <td><span style="float:right">{{$value->debitAccount->account_code}}</span></td>
                            <td><span style="float:right">{{str_replace(',','.',number_format($value->amount))}}</span></td>
                        </tr>
						@else
						<tr>
                            <td><span style="float:right"></span></td>
                            <td><span style="float:right">{{$value->debitAccount->account_code}}</span></td>
                            <td><span style="float:right">{{str_replace(',','.',number_format($value->amount))}}</span></td>
                        </tr>
						@endif
						@endif
						@endforeach
						<tr>
                            <td><span style="float:right">TK Có:</span></td>
                            <td><span style="float:right">{{$creditAccount}}</span></td>
                            <td><span style="float:right">{{str_replace(',','.',number_format($sum[0]->sum))}}</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div style="position:absolute">
                <table class="table" style="font-size:15px;width:143mm;position:relative;top:170px">
                    <thead>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="width:300px">Người nộp tiền<div style="float:right">:</div>
                            </td>
                            <th colspan="5">
                                <div style="float:left">{{$receipt->payment_user}}</div>
                            </th>
                        </tr>
                        <tr>
                            <td>Đại diện đơn vị<div style="float:right">:</div>
                            </td>
                            <td colspan="5">{{$receipt->partyable_name}}</td>
                        </tr>
                        <tr>
                            <td style="vertical-align:top">Địa chỉ<div style="float:right">:</div>
                            </td>
                            <td colspan="5">
                                <div style="width:420px">{{$address}}</div>
                            </td>
                        </tr>
                        <tr>
                            <td>Nội dung thu<div style="float:right">:</div>
                            </td>
                            <td colspan="5">
                                <div>{{$receipt->description}}</div>
                            </td>
                        </tr>
                        <tr>
                            <td>Số tiền<div style="float:right">:</div>
                            </td>
                            <td colspan="5">
                                <div style="float:left"><b>{{str_replace(',','.',number_format($receipt->amount))}}
                                        đồng</b>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6">Bằng chữ : <span
                                    style="font-weight:bold">{{ucfirst(convert_number_to_words($receipt->amount))}}
                                    đồng</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div style="position:absolute">
                <span style="font-size:14px;position:relative;top:333px;left:3px">Kèm theo:_______________________________________
                    chứng từ gốc</span>
            </div>

            <div style="position:absolute">
                <span style="position:relative;top:356px">Giấy giới thiệu số:</span><span style="position:relative;right:-230px;top:356px">Ngày:</span><span
                        style="position:relative;top:356px;right:-67mm">/</span><span style="position:relative;top:356px;right:-73mm">/</span><br>
            </div>
            <div style="position:absolute">
                <span style="float:right;position:relative;right:-483px;top:388px">Nhận ngày.......tháng.......năm.........</span>
            </div>
            <div style="position:absolute">
                <table class="table" style="font-size:14px;width:201mm;position:relative;left:-33px;top:450px">
                    <thead>
                        <tr>
                            <th>Thủ trưởng đơn vị<br><span style="font-weight:normal">(Ký,họ tên)</span></th>
                            <th>Kế toán trưởng<br><span style="font-weight:normal">(Ký,họ tên)</span></th>
                            <th>Kế toán thanh toán<br><span style="font-weight:normal">(Ký,họ tên)</span></th>
                            <th>Người nộp tiền<br><span style="font-weight:normal">(Ký,họ tên)</span></th>
                            <th>Thủ quỹ<br><span style="font-weight:normal">(Ký,họ tên)</span></th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div style="position:absolute">
                <span style="position:relative;top:600px">________________________________________________________________________________________</span>
            </div>
        </div>
    </div>
</body>

</html>