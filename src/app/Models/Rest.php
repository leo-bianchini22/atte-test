<?php

namespace App\Models;

use App\Http\Controllers\AttendanceController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rest extends Model
{
    use HasFactory;

    protected $fillable = ['time_id', 'breakIn', 'breakOut', 'month', 'day', 'breakTime'];

    public function time()
    {
        return $this->belongsTo(Time::class);
    }

    //任意の月の勤怠をスコープ
    public function scopeGetMonthAttendance($query, $month)
    {
        return $query->where('month', $month);
    }

    //任意の月の勤怠をスコープ
    public function scopeGetDayAttendance($query, $day)
    {
        return $query->where('day', $day);
    }
}
