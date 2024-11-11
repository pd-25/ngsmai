<div id="updateAmountM" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Booking No.'): <span class="booking-No"></span></h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
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
        </div>
    </div>
</div>
