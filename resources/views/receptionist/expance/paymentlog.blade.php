@php
    use Carbon\Carbon;
@endphp
@extends('receptionist.layouts.app')

@section('panel')
    <div class="row ">

        <div class="col-lg-12" style="display-flex">
            <div class="row align-items-end mb-4">
                <div class="col-lg-8">
                    <form class="d-flex flex-wrap gap-2" action="{{ route('receptionist.expense_managment.paymentlog') }}" method="get">
                        @csrf
            
                        

                               <input name="date" type="text" data-range="true" data-multiple-dates-separator=" - " data-language="en"
                    class="datepicker-here bg--white" data-position='bottom right' placeholder="@lang('From - To')"
                    autocomplete="off" value="{{ request()->date }}" style="max-width: 200px;">
            
                        <select name="type" class="form-control form--control" style="max-width: 180px;">
                            <option value="">@lang('Select Type')</option>
                            <option value="RECEIVED" @selected(request()->type == 'RECEIVED')>@lang('Received')</option>
                            <option value="RETURNED" @selected(request()->type == 'RETURNED')>@lang('Refund')</option>
                        </select>
            
                        <button class="btn btn--primary" type="submit">
                            <i class="fa fa-search"></i> @lang('Search')
                        </button>
            
                        <a href="{{ route('receptionist.expense_managment.paymentlog') }}" class="btn btn-secondary">
                            @lang('Reset')
                        </a>
                    </form>
                </div>
            
                <div class="col-lg-4 text-end">
                    <button class="btn btn-primary mb-2" id="printButton">@lang('Print')</button>
                    
                </div>
            </div>
        </div>
        <div class="col-lg-12">



            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table--light table" id="printTable">
                            <thead>
                                <tr>
                                    <th>@lang('S.N.')</th>
                                    <th style="text-align:left;">@lang('Booking ID')</th>
                                    <th style="text-align:left;">@lang('Amount')</th>
                                    <th style="text-align:left;">@lang('Payment Mode')</th>
                                    <th style="text-align:left;">@lang(' Type')</th>
                                    <th style="text-align:left;">@lang('Date')</th>
                                </tr>
                            </thead>
                            <tbody>



                                @php
                                    $Totalcredit = 0;
                                    $Totaldebit = 0;
                                    $i = 1;

                                @endphp
                                @forelse($paymentLogs as $item)
                                    <tr>
                                        <td data-label="@lang('S.N.')">{{ $loop->iteration }}</td>
                                        <td data-label="@lang('Booking ID')">{{ $item?->booking?->booking_number }}</td>
                                        <td data-label="@lang('Amount')">{{ number_format($item?->amount, 2) }}</td>
                                        <td data-label="@lang('Payment type')">{{ $item?->payment_mode }}</td>
                                        <td data-label="@lang('Payment type')"><span class="{{$item?->type == "RECEIVED" ? "text-success" : "text-danger"}}"><b>{{ $item?->type != "RECEIVED" ? 'REFUND' : $item?->type }}</b></span></td>
                                        <td data-label="@lang('Date')">{{ date('d M, Y h:i A', strtotime($item?->created_at)) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ $emptyMessage }}</td>
                                    </tr>
                                @endforelse


                            </tbody>



                        </table>

                    </div>


                </div>


                <div class="card-footer py-4">

                    <div>
                        <span><b>Total Amount</b> : {{ $totalAmount }}.00</span>
                    </div>
                </div>

            </div><!-- card end -->
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('assets/global/js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/vendor/datepicker.en.js') }}"></script>
    <script>
        "use strict";

        $('[name=type]').on('change', function() {
            $('.form-search').submit();
        })

        @if (request()->type)
            let type = @json(request()->type);
            $(`[name=type] option[value="${type}"]`).prop('selected', true);
        @endif
    </script>

    <script>
        // Function to initiate the print action
        document.getElementById('printButton').addEventListener('click', function() {

            printTable();
        });

        // Function to print the table
        function printTable() {
            var credit = "<?php echo $Totalcredit; ?>";
            var debit = "<?php echo $Totaldebit; ?>";
            var total_credit_debit = "<?php echo $Totalcredit - $Totaldebit; ?>";
            var printContainer = document.getElementById('printTable').outerHTML;
            var printWindow = window.open('', '_blank');
            printWindow.document.write('<html><head><title>Booking Actions Report</title>');
            printWindow.document.write('<style>');
            printWindow.document.write('table, th, td { border: 1px solid black;border-collapse:collapse; }');
            printWindow.document.write('table { width: 100%; }');
            printWindow.document.write('@media print { table { border-collapse: collapse; width: 100%; } }');
            printWindow.document.write('</style>');
            printWindow.document.write('</head><body>');
            printWindow.document.write(printContainer);
            printWindow.document.write('<br><div><span><b>Total Credit</b> : ' + credit +
                '.00</span>&nbsp;&nbsp;&nbsp;<span><b>Total Debit</b> : ' + debit +
                '.00</span><br><span><b>Credit - Debit</b> : ' + total_credit_debit + '.00</span></div>');
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.print();
        }
    </script>
    <script>
        (function($) {
            "use strict";

            if (!$('.datepicker-here').val()) {
                $('.datepicker-here').datepicker({});
            }

        })(jQuery);
    </script>
@endpush
