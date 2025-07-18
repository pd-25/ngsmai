<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Lib\CurlRequest;
use App\Models\AdminNotification;
use App\Models\Deposit;
use App\Models\User;
use App\Models\Room;
use App\Models\BookedRoom;
use App\Models\Booking;
use App\Models\BookingRequest;
use App\Models\PaymentLog;
use App\Models\UserLogin;
use App\Rules\FileTypeValidate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;


class AdminController extends Controller
{
    public function dashboard()
    {
        $pageTitle                         = 'Dashboard';
        // User Info
        $widget['total_users']             = User::count();
        $widget['verified_users']          = User::where('status', 1)->where('ev', 1)->where('sv', 1)->count();
        $widget['email_unverified_users']  = User::emailUnverified()->count();
        $widget['mobile_unverified_users'] = User::mobileUnverified()->count();

        $hotel['today_booked']             = BookedRoom::active()->where('booked_for', Carbon::now()->format('Y-m-d'))->count();
        $hotel['today_available']          = Room::active()
            ->whereNotIn('id', BookedRoom::active()
                ->where('booked_for', Carbon::now()->format('Y-m-d'))
                ->pluck('room_id')
                ->toArray())
            ->count();
        $hotel['booking_requests']         = BookingRequest::initial()->count();
        $hotel['running_bookings']         = Booking::active()->count();

        // user Browsing, Country, Operating Log
        $userLoginData                 = UserLogin::where('created_at', '>=', Carbon::now()->subDay(30))->get(['browser', 'os', 'country']);

        $chart['user_browser_counter'] = $userLoginData->groupBy('browser')->map(function ($item, $key) {
            return collect($item)->count();
        });

        $chart['user_os_counter']      = $userLoginData->groupBy('os')->map(function ($item, $key) {
            return collect($item)->count();
        });
        $chart['user_country_counter'] = $userLoginData->groupBy('country')->map(function ($item, $key) {
            return collect($item)->count();
        })->sort()->reverse()->take(5);


        $deposit['total_deposit_amount']   = Deposit::successful()->sum('amount');
        $deposit['total_deposit_pending']  = Deposit::pending()->count();
        $deposit['total_deposit_rejected'] = Deposit::rejected()->count();
        $deposit['total_deposit_charge']   = Deposit::successful()->sum('charge');

        // Monthly Booking
        $report['months']               = collect([]);
        $report['booking_month_amount'] = collect([]);
        $report['booking_cancel_amount'] = collect([]);

        $bookingMonth  = BookedRoom::where('booked_for', '>=', Carbon::now()->subYear())
            ->whereIn('status', [1, 9])
            ->selectRaw("SUM( CASE WHEN status IN(1,9) THEN fare END) as bookingAmount")
            ->selectRaw("DATE_FORMAT(booked_for,'%M-%Y') as months")
            ->orderBy('created_at')
            ->groupBy('months')
            ->get();

        $bookingMonth->map(function ($bookingData) use ($report) {
            $report['months']->push($bookingData->months);
            $report['booking_month_amount']->push(getAmount($bookingData->bookingAmount));
        });


        $trxReport['date'] = collect([]);
        $plusTrx = PaymentLog::where('type', 'RECEIVED')->where('created_at', '>=', Carbon::now()->subDays(30))
            ->selectRaw("SUM(amount) as amount, DATE_FORMAT(created_at,'%Y-%m-%d') as date")
            ->orderBy('created_at')
            ->groupBy('date')
            ->get();

        $plusTrx->map(function ($trxData) use ($trxReport) {
            $trxReport['date']->push($trxData->date);
        });

        $minusTrx = PaymentLog::where('type', 'RETURNED')->where('created_at', '>=', Carbon::now()->subDays(30))
            ->selectRaw("SUM(amount) as amount, DATE_FORMAT(created_at,'%Y-%m-%d') as date")
            ->orderBy('created_at')
            ->groupBy('date')
            ->get();

        $minusTrx->map(function ($trxData) use ($trxReport) {
            $trxReport['date']->push($trxData->date);
        });

        $trxReport['date'] = dateSorting($trxReport['date']->unique()->toArray());



        $months = $report['months'];

        for ($i = 0; $i < $months->count(); ++$i) {
            $monthVal      = Carbon::parse($months[$i]);
            if (isset($months[$i + 1])) {
                $monthValNext = Carbon::parse($months[$i + 1]);
                if ($monthValNext < $monthVal) {
                    $temp = $months[$i];
                    $months[$i]   = Carbon::parse($months[$i + 1])->format('F-Y');
                    $months[$i + 1] = Carbon::parse($temp)->format('F-Y');
                } else {
                    $months[$i]   = Carbon::parse($months[$i])->format('F-Y');
                }
            }
        }

        $today = Carbon::today()->toDateString();
        $tomorrow = Carbon::tomorrow()->toDateString();
        $todaysCheckout = BookedRoom::with("booking.user")->select('booked_rooms.booking_id', 'bookings.booking_number', 'last_date', DB::raw('GROUP_CONCAT(rooms.room_number) as rooms'))
            // ->join('bookings', 'booked_rooms.booking_id', '=', 'bookings.id')
            ->join('bookings', function ($join) {
                $join->on('booked_rooms.booking_id', '=', 'bookings.id')
                    ->where('bookings.is_manual_checkout', 0); // move the condition into the join
            })
            ->join('rooms', 'booked_rooms.room_id', '=', 'rooms.id')
            ->join(DB::raw('(SELECT booking_id, MAX(booked_for) as last_date FROM booked_rooms GROUP BY booking_id) as max_dates'), function ($join) {
                $join->on('booked_rooms.booking_id', '=', 'max_dates.booking_id')
                    ->on('booked_rooms.booked_for', '=', 'max_dates.last_date');
            })
            ->whereDate('max_dates.last_date', $today)
            ->groupBy('booked_rooms.booking_id', 'bookings.booking_number', 'last_date')
            ->get();
        $tomorrowsCheckout = BookedRoom::with("booking.user")->select('booked_rooms.booking_id', 'bookings.booking_number', 'last_date', DB::raw('GROUP_CONCAT(rooms.room_number) as rooms'))
            ->join('bookings', 'booked_rooms.booking_id', '=', 'bookings.id')
            ->join('rooms', 'booked_rooms.room_id', '=', 'rooms.id')
            ->join(DB::raw('(SELECT booking_id, MAX(booked_for) as last_date FROM booked_rooms GROUP BY booking_id) as max_dates'), function ($join) {
                $join->on('booked_rooms.booking_id', '=', 'max_dates.booking_id')
                    ->on('booked_rooms.booked_for', '=', 'max_dates.last_date');
            })
            ->whereDate('max_dates.last_date', $tomorrow)
            ->groupBy('booked_rooms.booking_id', 'bookings.booking_number', 'last_date')
            ->get();
        // dd($tomorrowsCheckout);

        return view('admin.dashboard', compact('pageTitle', 'widget', 'chart', 'deposit', 'report', 'bookingMonth', 'months', 'hotel', 'trxReport', 'plusTrx', 'minusTrx', 'todaysCheckout', 'tomorrowsCheckout'));
    }


    public function profile()
    {
        $pageTitle = 'Profile';
        $admin = auth('admin')->user();
        return view('admin.profile', compact('pageTitle', 'admin'));
    }
    public function course()
    {

        return view('admin.caurse.add_course');
    }

    public function profileUpdate(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'image' => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])]
        ]);
        $user = auth('admin')->user();

        if ($request->hasFile('image')) {
            try {
                $old = $user->image ?: null;
                $user->image = fileUploader($request->image, getFilePath('adminProfile'), getFileSize('adminProfile'), $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();
        $notify[] = ['success', 'Profile updated successfully'];
        return to_route('admin.profile')->withNotify($notify);
    }


    public function password()
    {
        $pageTitle = 'Password Setting';
        $admin = auth('admin')->user();
        return view('admin.password', compact('pageTitle', 'admin'));
    }

    public function passwordUpdate(Request $request)
    {
        $this->validate($request, [
            'old_password' => 'required',
            'password' => 'required|min:5|confirmed',
        ]);

        $user = auth('admin')->user();
        if (!Hash::check($request->old_password, $user->password)) {
            $notify[] = ['error', 'Password doesn\'t match!!'];
            return back()->withNotify($notify);
        }
        $user->password = bcrypt($request->password);
        $user->save();
        $notify[] = ['success', 'Password changed successfully.'];
        return to_route('admin.password')->withNotify($notify);
    }

    public function notifications()
    {
        $notifications = AdminNotification::orderBy('id', 'desc')->with('user')->paginate(getPaginate());
        $pageTitle = 'Notifications';
        return view('admin.notifications', compact('pageTitle', 'notifications'));
    }


    public function notificationRead($id)
    {
        $notification = AdminNotification::findOrFail($id);
        $notification->read_status = 1;
        $notification->save();
        $url = $notification->click_url;
        if ($url == '#') {
            $url = url()->previous();
        }
        return redirect($url);
    }

    public function requestReport()
    {
        $pageTitle = 'Your Listed Report & Request';
        $arr['app_name'] = systemDetails()['name'];
        $arr['app_url'] = env('APP_URL');
        $arr['purchase_code'] = env('PURCHASE_CODE');
        $url = "https://license.viserlab.com/issue/get?" . http_build_query($arr);
        $response = CurlRequest::curlContent($url);
        $response = json_decode($response);
        if ($response->status == 'error') {
            return to_route('admin.dashboard')->withErrors($response->message);
        }
        $reports = $response->message[0];
        return view('admin.reports', compact('reports', 'pageTitle'));
    }

    public function reportSubmit(Request $request)
    {
        $request->validate([
            'type' => 'required|in:bug,feature',
            'message' => 'required',
        ]);
        $url = 'https://license.viserlab.com/issue/add';

        $arr['app_name'] = systemDetails()['name'];
        $arr['app_url'] = env('APP_URL');
        $arr['purchase_code'] = env('PURCHASE_CODE');
        $arr['req_type'] = $request->type;
        $arr['message'] = $request->message;
        $response = CurlRequest::curlPostContent($url, $arr);
        $response = json_decode($response);
        if ($response->status == 'error') {
            return back()->withErrors($response->message);
        }
        $notify[] = ['success', $response->message];
        return back()->withNotify($notify);
    }

    public function readAll()
    {
        AdminNotification::where('read_status', 0)->update([
            'read_status' => 1
        ]);
        $notify[] = ['success', 'Notifications read successfully'];
        return back()->withNotify($notify);
    }

    public function downloadAttachment($fileHash)
    {
        $filePath = decrypt($fileHash);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $general = gs();
        $title = slug($general->site_name) . '- attachments.' . $extension;
        $mimetype = mime_content_type($filePath);
        header('Content-Disposition: attachment; filename="' . $title);
        header("Content-Type: " . $mimetype);
        return readfile($filePath);
    }
}
