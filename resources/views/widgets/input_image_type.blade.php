<div class="custom-file">
    <input type="file" name="{{$column}}" value=""  class="custom-file-input upload-image">
    <label class="custom-file-label">Choose file</label>
    <div class="image-thumbnail">
        <div class="row">
            <img src="" class="mr-1 click-image-thumbnail d-none" alt="img placeholder" height="40" width="40" data-toggle="modal" data-backdrop="false" data-target="#backdrop">
        </div>
        <div class="modal fade text-left" id="backdrop" tabindex="-1" role="dialog" data-toggle="modal" data-backdrop="false" data-target="#backdrop">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document" >
                <div class="modal-content">
                    <div class="modal-body">
                        <img src="" class="image-thumbnail mr-1 w-100" alt="img placeholder">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>