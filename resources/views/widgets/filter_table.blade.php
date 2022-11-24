@if($table == 'volume_trackings')
<div id="warehouse-location">
    <select class="form-control filter-table-column select2-filter-table">
        <option value="0">Tất cả khu vực</option>
        @foreach($paramSelects['area'] as $areaId)
        <option value="{{$areaId->name}}" area_id="{{$areaId->id}}">{{$areaId->name}}</option>
        @endforeach
    </select>
</div>
<div id="export-btn">
    @role('admin|QS|user')
    <button id="export-btn-click" class="ml-1 btn btn-primary" style="height:47px" tabindex="0"><span><i
                class="feather icon-arrow-down"></i> Export</span></button>
    @endrole
    @role('admin|QS')
    <button type="button" style="height:47px" id="click-modal" class="ml-1 btn btn-primary"><i
            class='feather icon-plus'></i> Import</button>
    @endrole
</div>
@elseif($table == 'users')
<div id="warehouse-location" style="margin-right:-5px">
    <div class="dropdown" style="float:left">
        <button class="btn btn-primary dropdown-toggle mr-1" type="button" id="dropdownMenuButton"
            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="height:45px">
            Phân vai trò
        </button>
        <form class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <table class="table">
                @foreach($roles as $role)
                <tr>
                    <td>
                        <label class="dropdown-item">
                            <fieldset>
                                <div class="vs-checkbox-con vs-checkbox-primary">
                                    <input type="radio" role="{{$role->id}}" name="roles" value="{{$role->name}}">
                                    <span class="vs-checkbox" id="vs-checkbox-role-{{$role->id}}">
                                        <span class="vs-checkbox--check">
                                            <i class="vs-icon feather icon-check"></i>
                                        </span>
                                    </span>
                                    <span>{{$role->name}}</span>
                                </div>
                            </fieldset>
                        </label>
                    </td>
                </tr>
                @endforeach
            </table>
            <button type="button" style="width:98px; height:38px; margin-left:16px" id="set-role-users"
                class="btn btn-primary">Cập nhật</button>
        </form>
    </div>
    <div class="dropdown" style="float:left">
        <button class="btn btn-primary dropdown-toggle mr-1" type="button" id="dropdownMenuButton"
            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="height:45px">
            Phân quyền
        </button>
        <form class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <table class="table">
                @foreach($permissions as $permission)
                <tr>
                    <td>
                        <label class="dropdown-item">
                            <fieldset>
                                <div class="vs-checkbox-con vs-checkbox-primary">
                                    <input type="checkbox" permission="{{$permission->id}}" name="permissions"
                                        value="{{$permission->name}}">
                                    <span class="vs-checkbox" id="vs-checkbox-permission-{{$permission->id}}">
                                        <span class="vs-checkbox--check">
                                            <i class="vs-icon feather icon-check"></i>
                                        </span>
                                    </span>
                                    <span>{{$permission->name}}</span>
                                </div>
                            </fieldset>
                        </label>
                    </td>
                </tr>
                @endforeach
            </table>
            <button type="button" style="width:98px; height:38px; margin-left:16px" id="set-permission-users"
                class="btn btn-primary">Cập nhật</button>
        </form>
    </div>
</div>
@else
<div id="export-btn">
    @role('admin|QS|user')
    <button id="export-btn-click" class="ml-1 btn btn-primary" style="height:47px" tabindex="0"><span><i
                class="feather icon-arrow-down"></i> Export</span></button>
    @endrole
</div>
@endif