<div class="modal fade text-left" id="import" tabindex="-1" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel16">Import</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12 data-field-col data-list-upload">
                        <form method="POST"  id="dataListUpload" action="{{ route('users.import')}}"  enctype="multipart/form-data" autocomplete="off">
                            @csrf
                            @method('post')
                            <input class="dz-message" type="file" name="import" id="import-user">
                            <br>
                            <a class="dz-message" href="{{asset('app-assets/sample-data/import-user.xlsx')}}">Sample DATA</a>
                            <div class="add-data-footer d-flex justify-content-around px-3 mt-2">
                                <div class="add-data-btn">
                                    <button type="submit" class="btn btn-primary">Import</button>
                                </div>
                                <div class="cancel-data-btn">
                                    <button type="button"  class="btn btn-outline-danger">Cancel</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

