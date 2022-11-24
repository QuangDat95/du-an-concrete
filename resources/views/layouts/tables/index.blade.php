@extends('layouts.master')
@section('content')
@php
$tablePrefix = $table .'.';
@endphp
@if($table == 'volume_trackings')
@push('css')
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
@endpush
@endif
<div class="action-btns d-none">
    @widget('FilterTable', ['table' => $table,'paramSelects'=>($paramSelects ?? '')])
    <div class="btn-dropdown mr-1 mb-1">
        @role('admin|QS')
        <div class="btn-group-delete dropdown actions-dropodown ml-2">
            <button type="button" class="btn btn-white px-1 py-1 dropdown-toggle waves-effect waves-light "
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Actions
            </button>
            <div class="dropdown-menu">
                <a id="delete-params" type="button" class="dropdown-item action-delete"><i
                        class="feather icon-trash-2"></i>Xoá</a>
            </div>
        </div>
        @endrole
    </div>
</div>
</select>
<section id="data-list-view" class="data-list-view-header">
    <div class="d-none">
        <div class="custom-control filter-on-off d-inline-block custom-switch ml-2 mb-1">
            <input type="checkbox" class="custom-control-input is-filter-grid" id="customSwitch80">
            Filter:
            <label class="custom-control-label" for="customSwitch80">
                <span class="switch-text-left">On</span>
                <span class="switch-text-right">Off</span>
            </label>
        </div>
    </div>
    <div class="table-responsive">
        <div class="filters d-none">
            <tr></tr>
        </div>
        {!! $dataTable->table() !!}
    </div>
    @include('layouts.tables.edit')
</section>
@push('js')
{!! $dataTable->scripts() !!}
<script src="{{ mix('dashboards/js/reorder-form.js') }}"></script>
@if($table == 'users')
<script src="{{ mix('dashboards/js/permission.js') }}"></script>
@endif
@if($table == 'volume_trackings')
<script>
    $(document).ready(function(){
    $('input[name="pumping_time_begin"],input[name="pumping_time_finish"]').timepicker({
        appendWidgetTo: 'body',
        showSeconds: false,
        showMeridian: !1,
        defaultTime: true,
        timeFormat: 'HH:mm',
        showMeridian: false
    });
});
</script>
<script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
@endif
<script src="{{ mix('dashboards/js/data-table-list.js') }}"></script>
<script src="{{ mix('dashboards/js/volume_trackings/index.js') }}"></script>
<script src="{{ mix('dashboards/js/export.js') }}"></script>
<script>
const BASE_URL = "{{ URL::to('/') }}/image/loading.gif";
const URL_DELETE_API = "{{route($table.'.delete')}}";
const TABLE_NAME = "{{$table}}";
const INPUT_TYPE_SELECT = "{{json_encode(inputTypeSelect())}}";
const INPUT_FORMAT_PRICE = "{{json_encode(getInputFormatPrice())}}";
const URL_EXPORT_VOLUME = "{{route('volumetrackingexport')}}";
const URL_EXPORT_CUSTOMER = "{{route('customerexport')}}";
const URL_EXPORT_CONSTRUCTION = "{{route('constructionexport')}}";
const USER_ID = "{{Auth::user()->id}}";
const USER_ROLE_NAME = "{{(Auth::user()->roles[0]->name)}}";
</script>
@endpush
@endsection