<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExpanceManagment;
use App\Models\Booking;
use App\Models\Receptionist;
use App\Models\PaymentLog;
use App\Rules\FileTypeValidate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Carbon\Carbon;

class ExpanceReportController extends Controller
{
    public function index()
    {

        $pageTitle = 'All Expense';
        $data = ExpanceManagment::all();

        $table2Data = $this->bookingData('ALL');

        $users = Receptionist::all();
        $customer = Booking::all();
        $user_id = 0;
        $customer_id = 0;

        return view('admin.expance.expance_report', compact('pageTitle', 'data', 'table2Data', 'users', 'user_id', 'customer', 'customer_id'));
    }


    public function filter_by_date(Request $request)
    {
        $pageTitle = 'All Expense';
        $data = ExpanceManagment::all();

        $table2Data = $this->bookingData('ALL');

        $users = Receptionist::all();
        $customer = Booking::all();
        $user_id = 0;
        $customer_id = 0;
        return view('admin.expance.expance_report', compact('pageTitle', 'data', 'table2Data', 'users', 'user_id', 'customer', 'customer_id'));
    }



    public function filter_by_user(Request $request)
    {

        $pageTitle = 'All Expense';
        $data = ExpanceManagment::all();
        if ($request->id) {
            $user_id = $request->id;
        }

        $table2Data = $this->bookingData('ALL');

        $users = Receptionist::all();
        $customer = Booking::all();
        if ($request->id) {
            $user_id = $request->id;
        } else {
            $user_id = 0;
        }
        $customer_id = 0;
        return view('admin.expance.expance_report', compact('pageTitle', 'data', 'table2Data', 'users', 'user_id', 'customer', 'customer_id'));
    }

    public function filter_by_customer(Request $request)
    {

        $pageTitle = 'All Expense';
        $data = ExpanceManagment::all();

        $table2Data = $this->bookingData('ALL');

        $users = Receptionist::all();
        $customer = Booking::all();

        if ($request->customer_id) {
            $customer_id = $request->customer_id;
        } else {
            $customer_id = 0;
        }
        $user_id = 0;
        return view('admin.expance.expance_report', compact('pageTitle', 'data', 'table2Data', 'users', 'user_id', 'customer', 'customer_id'));
    }

    public function store(Request $request, $id = null)
    {
        $validation_rule = [
            'debit' => 'required',
            'credit' => 'required',
            'date' => 'required',
            'particulars' => 'required',
            'expense_type' => 'required',
            'expense_category' => 'required'
        ];

        if ($id == 0) {
            $expance = new ExpanceManagment();
            $notify[] = ['success', 'Expense Created Successfully'];
        } else {
            $expance = ExpanceManagment::findOrFail($id);
            $notify[] = ['success', 'Expense Updated Successfully'];
        }

        $request->validate($validation_rule, [
            'debit' => 'required',
            'credit' => 'required',
            'date' => 'required',
            'particulars' => 'required',
            'expense_type' => 'required',
            'expense_category' => 'required'
        ]);

        $expance->debit = $request->debit;
        $expance->credit = $request->credit;
        $expance->date = $request->date;
        $expance->particulars = $request->particulars;
        $expance->expense_type = $request->expense_type;
        $expance->expense_category = $request->expense_category;

        $expance->save();

        return redirect()->back()->withNotify($notify);
    }

    protected function validateRecipient($request, $id)
    {
        $rules = [
            'debit' => 'required',
            'credit' => 'required',
            'date' => 'required',
            'particulars' => 'required',
            'expense_type' => 'required',
            'expense_category' => 'required'
        ];

        $request->validate($rules);
    }

    public function remove($id)
    {
        $expance = ExpanceManagment::findOrFail($id);
        $expance->delete();
        $notify[] = ['success', 'Content removed successfully'];
        return back()->withNotify($notify);
    }

    protected function bookingData($scope)
    {
        $request = request();
        $query = Booking::query();

        if ($scope != "ALL") {
            $query = $query->$scope();
        }

        if ($request->search) {
            $search = $request->search;

            $query->whereHas('user', function ($q) use ($search) {
                $q->where('username', 'like', "%$search%")
                    ->orWhere('email', 'like',  "%$search%")
                    ->orWhere('mobile', 'like',  "%$search%");
            })->orWhere(function ($query) use ($search) {
                $query->where('guest_details->name', 'like', "%$search%")
                    ->orWhere('guest_details->email', 'like', "%$search%")
                    ->orWhere('guest_details->mobile', 'like', "%$search%")
                    ->orWhere('booking_number', 'like', "%" . $search . "%");
            });
        }

        if ($request->date) {
            $date  = explode('-', $request->date);

            $request->merge([
                'checkin_date'  => trim(@$date[0]),
                'checkout_date' => trim(@$date[1]) ? trim(@$date[1]) : trim(@$date[0])
            ]);

            $request->validate([
                'checkin_date'  => 'required|date_format:m/d/Y',
                'checkout_date' => 'nullable|date_format:m/d/Y|after_or_equal:checkin_date'
            ]);

            $checkIn  = Carbon::parse($request->checkin_date)->format('Y-m-d');
            $checkOut = Carbon::parse($request->checkout_date)->format('Y-m-d');

            $query->whereHas('bookedRoom', function ($q) use ($checkIn, $checkOut) {
                $q->where('booked_for', '>=', $checkIn)->where('booked_for', '<=', $checkOut);
            });
        }

        return $query->with('bookedRoom.room', 'user', 'paymentlog')
            ->withMin('bookedRoom', 'booked_for')
            ->withMax('bookedRoom', 'booked_for')
            ->withSum('usedExtraService', 'total_amount')
            ->orderBy('booked_room_min_booked_for', 'asc')
            ->latest()
            ->paginate(getPaginate());
    }

    public function paymentlog(Request $request)
    {
        $data["pageTitle"] = 'All Expense';
        $getLogs = PaymentLog::with("booking");
        // ->where('receptionist_id', auth()->guard('receptionist')->id())
        // dd($request->date);
        if ($request->date) {
            $dateRange  = explode('-', $request->date);
            if (count($dateRange) === 2) {
                // Convert the date range from MM/DD/YYYY to YYYY-MM-DD format
                $startDate = Carbon::createFromFormat('m/d/Y', trim($dateRange[0]))->startOfDay();
                $endDate = Carbon::createFromFormat('m/d/Y', trim($dateRange[1]))->endOfDay();

                // Apply the date range filter
                $getLogs->whereBetween('created_at', [$startDate, $endDate]);
            }
        }
        if ($request->type) {
            $getLogs->where('type', $request->type);
        }
        $allLogsQuery = (clone $getLogs);
        // $allReceivedLogsQuery = (clone $getLogs);
        // $allDebitLogsQuery = (clone $getLogs);
        $data["paymentLogs"] = $getLogs->orderBy('id', 'DESC')->paginate(30);
        $sum = $allLogsQuery->sum('amount');
        $data["totalAmount"] = rtrim(rtrim(number_format($sum, 2, '.', ''), '0'), '.');
        $sumReceive=  $allLogsQuery->where('type', 'RECEIVED')->sum('amount');
        $data["receivedAmount"] = rtrim(rtrim(number_format($sumReceive, 2, '.', ''), '0'), '.');
        $sumDebit=  $allLogsQuery->where('type', 'RETURNED')->sum('amount');
        $data["debitAmount"] = rtrim(rtrim(number_format($sumDebit, 2, '.', ''), '0'), '.');
        dd($data);

        // $data["paymentLogs"] = $getLogs->orderBy('id', 'DESC')->paginate(30);
        // $data["totalAmount"] = $data["paymentLogs"]->sum('amount');
        // $data["receivedAmount"] = $data["paymentLogs"]->where('type', 'RECEIVED')->sum('amount');
        // $data["debitAmount"] = $data["paymentLogs"]->where('type', 'RETURNED')->sum('amount');
        return view('admin.expance.paymentlog', $data);
    }
}
