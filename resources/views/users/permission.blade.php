@extends('layouts.master')
@section('content')
<section class="users-edit">
    <div class="card">
        <div class="card-content">
            <div class="card-body">
                <div class="tab-content">
                    @if(session('status'))
                    <div class="alert alert-success">
                        <a class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
                        <strong>{{session('status')}}</strong>
                    </div>
                    @endif
                    <form id="add-permission-user" method="POST">
                        <input type="hidden" name="id" value="{{$role_id}}">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive border rounded px-1 ">
                                    <h6 class="border-bottom py-1 mx-1 mb-0 font-medium-2"><i
                                            class="feather icon-lock mr-50 "></i>Cấp quyền cho {{$role->name}}
                                    </h6>
                                    <div class="custom-control custom-switch" style="margin-top:10px;margin-left:13px">
                                        <input type="checkbox" class="custom-control-input" id="selectAll">
                                        <label class="custom-control-label" for="selectAll"></label>
                                        <h6 style="margin-left:49px; margin-top:-20px">Chọn tất cả</h6>
                                    </div>
                                    <table class="table table-borderless">
                                        <thead>
                                            <tr>
                                                <th>Danh mục</th>
                                                <th>Đọc</th>
                                                <th>Thêm</th>
                                                <th>Sửa</th>
                                                <th>Xóa</th>
                                                <th>Export</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($categorys as $category)
                                            <tr>
                                                <td>{{config('default.category.'.$category)}}</td>
                                                @foreach($permissions as $permission)
                                                @if(strlen(strstr($permission->name,$category)) > 0)
                                                <td>
                                                    <div class="custom-control custom-switch">
                                                        <input class="custom-control-input" type="checkbox"
                                                            data-toggle="toggle" @foreach($get_permission_roles as
                                                            $value) @if($permission->name == $value)
                                                        checked
                                                        @endif
                                                        @endforeach
                                                        name="permission[]" value="{{$permission->id}}"
                                                        id="users-checkbox{{$permission->id}}">
                                                        <label class="custom-control-label"
                                                            for="users-checkbox{{$permission->id}}"></label>
                                                    </div>
                                                </td>
                                                @endif
                                                @endforeach
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
                                <button type="submit"
                                    class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1">Lưu</button>
                                <a type="button" href="{{route('users')}}" class="btn btn-outline-warning">trở về</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@push('js')
<script>
if ($(".custom-control-input[name^='permission']:checked").length == $(
        ".custom-control-input[name^='permission']").length) {
    $("#selectAll").prop("checked", true)
};
$("#selectAll").click(function() {
    $("input[type=checkbox]").prop('checked', $(this).prop('checked'));
});
$(".custom-control-input[name^='permission']").change(function() {
    if ($(this).prop("checked") == false) {
        $("#selectAll").prop("checked", false)
    };
    if ($(".custom-control-input[name^='permission']:checked").length == $(
            ".custom-control-input[name^='permission']").length) {
        $("#selectAll").prop("checked", true)
    };
})
</script>
<script src="{{mix('dashboards/js/reorder-form.js')}}"></script>
@endpush
@endsection