<div class="modal fade" id="cancelBookingModal" tabindex="-1" role="dialog" aria-labelledby="cancelBookingModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelBookingModalLabel">@lang('Cancel Booking')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="cancelBookingForm">
                    <div class="form-group">
                        <label for="reason">@lang('Reason for Cancellation')</label>
                        <textarea id="reason" class="form-control" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('Close')</button>
                <button type="button" class="btn btn-danger" id="confirmCancelBtn">@lang('Delete')</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        let cancelActionUrl;

        // Capture the click event on the cancel link
        $('.confirmationBtn').on('click', function() {
            cancelActionUrl = $(this).data('action');
            $('#cancelBookingModal').modal('show');
        });

        // Handle the confirm cancel button click
        $('#confirmCancelBtn').on('click', function() {
            const reason = $('#reason').val();

            if (reason.trim() === '') {
                alert('@lang("Please provide a reason for cancellation.")');
                return;
            }

            $.ajax({
                url: cancelActionUrl,
                type: 'POST', 
                data: {
                    reason: reason,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#cancelBookingModal').modal('hide');
                    location.reload(); 
                },
                error: function(xhr) {
                    alert('@lang("There was an error cancelling the booking. Please try again.")');
                }
            });
        });
    });

</script>