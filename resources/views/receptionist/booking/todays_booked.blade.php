@extends('receptionist.layouts.app')
@section('panel')

<div class="row mb-4">

    <h6 class="mb-2">Booking Status</h6>
    <div class="col-md-4 mb-2">
        <div class="card bg-info text-white">
            <div class="card-header text-center">
                <h6>{{ $totalRooms_1?->name }}</h6>
            </div>
            <div class="card-body text-center">
                <p><b>{{ $totalRooms_1?->total_adult - $occupiedRooms_1 }}</b> out of <b>{{ $totalRooms_1?->total_adult }}</b></p>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-2">
        <div class="card bg-info text-white">
            <div class="card-header text-center">
                <h6>{{ $totalRooms_2?->name }}</h6>
            </div>
            <div class="card-body text-center">
                <p><b>{{ $totalRooms_2?->total_adult - $occupiedRooms_2 }}</b> out of <b>{{ $totalRooms_2?->total_adult }}</b></p>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-2">
        <div class="card bg-info text-white">
            <div class="card-header text-center">
                <h6>{{ $totalRooms_3?->name }}</h6>
            </div>
            <div class="card-body text-center">
                <p><b>{{ $totalRooms_3?->total_adult - $occupiedRooms_3}}</b> out of <b>{{ $totalRooms_3?->total_adult }}</b></p>
            </div>
        </div>
    </div>

</div>

<div class="row mb-2">
    <h6>Inventory Stock</h6>
    @forelse ($ExtraService as $Extra)
    <div class="col-md-4 mb-2 mt-2">
        <div class="card bg-info text-white">
            <?php
            $UsedExtraServices =   DB::table('used_extra_services')->where('extra_service_id', $Extra->id)->where('service_date', $selectedDate)->get();
            $quantity = 0;
            dd($UsedExtraServices);
            if ($UsedExtraServices) {
                foreach ($UsedExtraServices as $UsedServices) {
                    $quantity += $UsedServices->qty;
                }
            }
            ?>
            <div class="card-header text-center">
                <h6>{{ $Extra->name }}</h6>
            </div>
            <div class="card-body text-center">
                <p>{{ $Extra->qty - $quantity}}</b> out of <b>{{ $Extra->qty }}</b></p>
            </div>
        </div>
    </div>
    @empty

    @endforelse


</div>
<!--pd-->
<div class="row">
<div class="col-lg-12">
      <h5 class="card-title mt-3 p-2">Today's Checkout</h5>
    <div class="card b-radius--10">
        <div class="card-body p-0">
            <div class="table-responsive--md table-responsive" id="printContainer">
                <table class="table--light table" id="printTable">
                    <thead>
                        <tr>
                            <th>@lang('S.N.')</th>
                            <th style="text-align:left;">@lang('Booking Number')</th>
                            <th style="text-align:left;">@lang('Last Date')</th>
                            <th style="text-align:left;">@lang('Room Numbers')</th>
                        </tr>
                    </thead>
                    <tbody>
                       
                        @forelse ($todaysCheckout as $item)
                            <tr>
                                <td data-label="@lang('S.N.')">{{ $loop->iteration }}</td>
                                <td style="text-align:left;" data-label="@lang('Booking Number')">
                                    <a href="{{ route('receptionist.booking.details', $item->booking_id) }}"> {{ __($item->booking_number) }}
                                   <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6" width="16" height="16">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                    </svg>

                                   </a>
                                </td>
                                <td style="text-align:left;" data-label="@lang('Checkout date')">
                                    {{ showDateTime($item->last_date, 'd M, Y') }} 
                                   
                                </td>
                                <td style="text-align:left;" data-label="@lang('Checkout date')">
                                    {{ $item->rooms }} 
                                   
                                </td>
                                
                            </tr>
                            
                        @empty
                            <tr>
                                <td class="text-muted text-center" colspan="100%">{{ "No checkout today" }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer py-4"></div>
    </div>
    <!-- card end -->
</div>
</div>

<div class="row">
<div class="col-lg-12">
      <h5 class="card-title mt-3 p-2">Tomorrow's Checkout</h5>
    <div class="card b-radius--10">
        <div class="card-body p-0">
            <div class="table-responsive--md table-responsive" id="printContainer">
                <table class="table--light table" id="printTable">
                    <thead>
                        <tr>
                            <th>@lang('S.N.')</th>
                            <th style="text-align:left;">@lang('Booking Number')</th>
                            <th style="text-align:left;">@lang('Last Date')</th>
                            <th style="text-align:left;">@lang('Room Numbers')</th>
                        </tr>
                    </thead>
                    <tbody>
                       
                        @forelse ($tomorrowsCheckout as $tomorrowsCheckou_t)
                            <tr>
                                <td data-label="@lang('S.N.')">{{ $loop->iteration }}</td>
                                <td style="text-align:left;" data-label="@lang('Booking Number')">
                                    <a href="{{ route('receptionist.booking.details', $tomorrowsCheckou_t->booking_id) }}"> {{ __($tomorrowsCheckou_t->booking_number) }}
                                   <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6" width="16" height="16">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                    </svg>

                                   </a>
                                </td>
                                <td style="text-align:left;" data-label="@lang('Checkout date')">
                                    {{ showDateTime($tomorrowsCheckou_t->last_date, 'd M, Y') }} 
                                   
                                </td>
                                <td style="text-align:left;" data-label="@lang('Checkout date')">
                                    {{ $tomorrowsCheckou_t->rooms }} 
                                   
                                </td>
                                
                            </tr>
                            
                        @empty
                            <tr>
                                <td class="text-muted text-center" colspan="100%">{{ "No checkout today" }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer py-4"></div>
    </div>
    <!-- card end -->
</div>
</div>

<div class="row gy-4">
    @forelse($rooms as $room)
    <div class="col-xxl-3 col-xl-4 col-lg-6 col-md-6">
        <div class="widget-two box--shadow2 b-radius--5 bg--white">
            <div class="widget-two__icon b-radius--5 bg--dark">
                {{ $room->room->room_number }}
            </div>
            <div class="widget-two__content d-flex align-items-end justify-content-between flex-wrap">

                <div>
                    @if ($room->booking)
                    @if ($room->booking->user_id)
                    <h3>
                        <a href="{{ route('admin.users.detail', $room->booking->user_id) }}" class="f-size--18 text-center text--dark"><?php echo isset($room->booking->user->fullname) ? $room->booking->user->fullname : ''; ?></a>
                    </h3>
                    @else
                    <h3 class="f-size--18 text--dark">{{ @$room->booking->guest_details->name }}</h3>
                    @endif

                    <div class="d-flex flex-column fw-bold w-100">
                        <p class="text--muted text--small">@lang('Booking No.'): <span class="fw-bold">{{ $room->booking->booking_number }}</span></p>
                        <p class="text--muted text--small">@lang('Room Type'): {{ __($room->room->roomType->name) }}</p>
                    </div>
                    @else
                    <h3 class="f-size--18 text--dark">@lang('Booking Not Found')</h3>
                    @endif
                </div>


                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('receptionist.extra.service.add') }}?room={{ $room->room->room_number }}" class="btn btn--xs btn-outline--dark" data-services="{{ $room->extraServices }}"> <i class="la la-plus"></i>@lang('Add Service')</a>

                    <button type="button" class="btn btn--xs btn-outline--info btn-view" data-services="{{ $room->extraServices }}"> <i class="la la-eye"></i>@lang('View Services')</button>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col">
        <div class="card">
            <div class="card-body">
                <h4 class="text--muted">@lang('No Room Booked Yet')</h4>
            </div>
        </div>
    </div>

    @endforelse
</div>

@if($emptyRooms->count())
<h3 class="my-3">@lang('Available for Booking')</h3>
<div class="row gy-4">
    @foreach ($emptyRooms as $room)
    <div class="col-xxl-2 col-sm-2 col-3">

        <div class="bg--white p-3 rounded text-center">
            <span class="d-block fw-bold">
                {{ $room->room_number }}
            </span>

            <span class="text--small">{{ __($room->roomType->name) }}</span>
        </div>
    </div>
    @endforeach
</div>
@endif

<!-- Modal -->
<div class="modal fade" id="extraServices" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Extra Services')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h5 class="text--center">@lang('No extra service yet.')</h5>
            </div>
        </div>
    </div>
</div>

@endsection


@push('script')
<script>
    (function($) {
        'use strict';
        $('.btn-view').on('click', function() {
            let modal = $('#extraServices');
            let services = $(this).data('services');
            let content = ``;
            if (services.length) {
                content += `<ul class="list-group-flush">
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="w-25">@lang('Name')</span>
                        <span class="w-25 text-center">@lang('Qty')</span>
                        <span class="w-25 text-center">@lang('Price')</span>
                        <span class="w-25 text-end">@lang('Total')</span>
                    </li>
                    `;

                services.forEach((element, i) => {
                    content += `<li class="list-group-item d-flex justify-content-between">
                            <span class="w-25">${i+1}. ${element.extra_service.name}</span>
                            <span class="w-25 text-center">${element.qty}</span>
                            <span class="w-25 text-center">{{ $general->cur_sym }}${parseFloat(element.unit_price)}</span>
                            <span class="w-25 text-end">{{ $general->cur_sym }}${parseFloat(element.total_amount)}</span>
                        </li>`;
                });

                content += `</ul>`;
            } else {
                content = `<h4 class="text-center">@lang('No service used yet')</h4>`;
            }
            modal.find('.modal-body').html(content);

            modal.modal('show');
        });

    })(jQuery);
</script>


@endpush