<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Time extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'punchIn', 'punchOut', 'month', 'day', 'workTime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeCreatedSearch($query, $created_at)
    {
        if (!empty($created_at)) {
            $query->where('created_at', 'like', '%' . $created_at . '%');
        }
    }

    // //任意の月の勤怠をスコープ
    // public function scopeGetMonthAttendance($query, $month)
    // {
    //     return $query->where('month', $month);
    // }

    // //任意の月の勤怠をスコープ
    // public function scopeGetDayAttendance($query, $day)
    // {
    //     return $query->where('day', $day);
    // }
}
