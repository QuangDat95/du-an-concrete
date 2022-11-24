@extends('layouts.master')
@section('content')
<div class="app-content content mt-1" style="margin-left:-48px;margin-right:-48px">
    <div class="row">
        <div class="card col-md-11 col-lg-11 m-auto">
            <div class="col-md-2 col-lg-2" style="padding-top:10px;">
                <a href="{{route('surveyRecords.show',['survey'=>$surveyRecord->survey_id])}}" class="btn btn-color-dufago"><i class="feather icon-arrow-left"></i> Back</a>
            </div>
            <div class="card-header">           
                <div class="card-title">Thông tin khảo sát:
                    @if($surveyRecord->status==2)
                        Phiếu khảo sát đạt
                    @elseif($surveyRecord->status==1)
                        Phiếu khảo sát không đạt
                        <label class="text-success text-bold-600 font-medium-1">Đã xử lý</label>
                    @else
                        Phiếu khảo sát không đạt
                        <div class="modal-warning mr-1 mb-1 d-inline-block">
                            <button class="btn btn-outline-danger" data-toggle="modal" data-target="#warning">Xử lý phiếu khảo sát</button>
                            <!-- Modal -->
                            <div class="modal fade text-left" id="warning" tabindex="-1" role="dialog" aria-labelledby="myModalLabel140" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title white" id="myModalLabel140">Xác Nhận</h5>
                                            <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button> -->
                                        </div>
                                        <div class="modal-body">
                                            Phiếu đã xử lý?
                                        </div>
                                        <div class="modal-footer">
                                            <a href="{{route('surveyRecords.handle',['surveyRecord'=>$surveyRecord->id])}}" class="btn btn-color-dufago">Đồng ý</a>
                                            <button type="button" class="btn btn-light" data-dismiss="modal">Hủy bỏ</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <div class="card-content">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped my-table border-top">
                            <thead style="border-top:1px solid #e6e6e6">
                                <th>Loại khảo sát</th>
                                <th>Nhân viên khảo sát</th>
                                <th>Mã khách hàng</th>
                                <th>Tên công trình</th>
                                <th>Địa chỉ</th>   
                                <th>Thời gian khảo sát</th>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{$surveyRecord->survey->name}}</td>
                                    <td>{{$surveyRecord->employee->hovaten}}</td>
                                    <td>{{$surveyRecord->customer->name}}</td>
                                    <td>{{$surveyRecord->construction->name}}</td>
                                    <td>{{$surveyRecord->address}}</td>
                                    <td>{{date('d/m/Y H:i:s',strtotime($surveyRecord->created_at))}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="card col-11 ml-auto mr-auto mt-2">
            <div class="card-header">
                <h4 class="card-title">Chi tiết khảo sát</h4>
            </div>
            <div class="card-content">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped my-table">
                            <thead>
                                <th>STT</th>
                                <th>Câu hỏi</th>
                                <th>Đạt</th>
                                <th>Không đạt</th>
                                <th>Câu trả lời khác</th>
                                <th>Lý do không đạt</th>
                            </thead>
                            <tbody>    
                                @php $i=0; @endphp                       
                                @foreach($surveyRecord->surveyDetails as $surveyDetail)
                                    <tr>
                                        <td>{{++$i}}</td>
                                        <td>{{$surveyDetail->answer->key}}</td>
                                        @if($surveyDetail->value=='value_2')
                                            <td></td>
                                            <td><i class="feather icon-check"></td>
                                            <td></td>
                                        @elseif($surveyDetail->value=='value_1')
                                            <td><i class="feather icon-check"></i></td>
                                            <td></td>
                                            <td></td>
                                        @else
                                            <td></td>
                                            <td></td>
                                            <td>{{$surveyDetail->value}}</td>
                                        @endif    
                                        <td>{{$surveyDetail->reason}}</td>                                                                   
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
@endsection