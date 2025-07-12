<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class PaymentLog extends Model
{
    protected $guarded = ['id'];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function receptionist()
    {
        return $this->belongsTo(Receptionist::class);
    }
     public function getCreatedAtAttribute($value)
    {
        // return Carbon::parse($value)->setTimezone(config('app.timezone'))->toDateTimeString();
        return Carbon::parse($value)->timezone('Asia/Kolkata')->toDateTimeString();
    }
}
