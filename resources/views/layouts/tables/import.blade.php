<section id="data-list-view" class="data-list-view-header">
    <div class="table-edit">
        <div class="add-new-data-sidebar">
            <div class="overlay-bg"></div>
            <div class="add-new-data" style="width: 50rem">
                <div class="div px-2 d-flex new-data-title justify-content-between {{in_array($table,tableTransaction()) ? 'white' : ''}}">
                    <div class="sidebar-arrow-left hide-data-sidebar">
                        <i class="feather icon-arrow-left"></i>
                    </div>
                    <div class="sidebar-title">
                        <h4 class="edit-volumetracking text-uppercase fw-bold title">Sửa</h4>
                    </div>
                    <div class="sidebar-more-vertical">
                        <div class="sidebar-actions">
                            <div class="d-flex flex-row-reverse">
                                @role('admin|QS')
                                <div class="btn-group">
                                    <div class="dropdown">
                                        <button type="text" class="btn waves-effect waves-light" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="feather icon-more-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <a class="from-edit dropdown-item" id="button-editer"><i class="feather icon-edit"></i>Sửa</a>
                                        </div>
                                    </div>
                                </div>
                                @endrole
                            </div>
                        </div>
                    </div>
                </div>
                <div class="data-items pb-3 data-items-unset">
                    <form method="post" id="form-data-list" action="{{ route($table)}}" enctype="multipart/form-data">
                        @csrf
                        <div class="data-fields mt-3 px-2">
                            <div class="row d-block">
                                <input type="hidden" name="code_old" id="code-old">
                                <input type="hidden" name="name_old" id="name-old">
                                <input type="hidden" name="area_id_primary" id="area_id_primary" value="">
                                <div class="col-md-12">
                                    <div class="tab-content">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                @role('admin|QS')
                <div class="add-data-footer d-none justify-content-around px-3 mt-2">
                    <div class="add-data-btn">
                        <button type="button" id="{{(in_array($table,tableTransaction())) ? 'action-approve-transaction' : 'btn-submit'}}" class="btn btn-primary">Lưu</button>
                    </div>
                    <div class="cancel-data-btn">
                        <button type="button" class="btn btn-outline-danger">Huỷ</button>
                    </div>
                </div>
                @endrole
            </div>
        </div>
    </div>
</section>