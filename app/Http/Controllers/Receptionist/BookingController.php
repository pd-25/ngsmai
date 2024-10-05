<?php

namespace App\Http\Controllers\Receptionist;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingRequest;
use App\Models\Deposit;
use App\Models\User;
use App\Traits\ManageBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    use ManageBooking;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = auth()->guard('receptionist')->user();
            return $next($request);
        });
        $this->userType = "receptionist";
        $this->column = "receptionist_id";
    }

    public function mobile(Request $request)
    {
        // $UserData = User::where('mobile', $request->mobile)->first();
        $UserData = User::where(DB::raw('RIGHT(mobile, 10)'), $request->mobile)->first();
        
        echo json_encode($UserData);
    }

    public function detail($id)
    {
        $user                        = User::findOrFail($id);
        $pageTitle                   = 'User Detail - ' . $user->username;

        $widget['total_bookings']    = Booking::where('user_id', $id)->count();
        $widget['running_bookings']  = Booking::active()->where('user_id', $id)->count();
        $widget['booking_requests']  = BookingRequest::initial()->where('user_id', $id)->count();
        $widget['total_payment']     = Deposit::successful()->where('user_id', $id)->sum('amount');

        $countries = json_decode(file_get_contents(resource_path('views/partials/country.json')));

        return view('receptionist.user.detail', compact('pageTitle', 'user', 'countries', 'widget'));
    }

   
}
