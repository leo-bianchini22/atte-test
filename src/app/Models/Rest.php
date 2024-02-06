<?php

namespace App\Models;

use App\Http\Controllers\AttendanceController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rest extends Model
{
    use HasFactory;

    protected $fillable = ['time_id', 'breakIn', 'breakOut', 'month', 'day', 'year', 'breakTime'];

    public function time()
    {
        return $this->belongsTo(Time::class);
    }

    public function scopeGetMonthAttendance($query, $month)
    {
        return $query->whereMonth('created_at', $month);
    }

    public function scopeGetDayAttendance($query, $day)
    {
        return $query->whereDay('created_at', $day);
    }
}
