<form action="" method="post">
    @csrf
    <div class="modal-body">
        <div class="form-group">
            <label class="required">@lang('Current Total Amount')</label>
            <div class="d-flex">
                <div class="input-group row gx-0">
                    <span class="form-control totalAmnt"></span>
                </div>
                
            </div>
        </div>
        <div class="form-group">
            <label class="required">@lang('Add Amount')</label>
            <div class="d-flex">
                <div class="input-group row gx-0">
                    <input type="number" class="form-control" name="new_amount" required>
                </div>
                
            </div>
        </div>
        <div class="more-bookings"></div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>
    </div>
</form>