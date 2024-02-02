<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Time extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'punchIn', 'punchOut', 'month', 'day', 'workTime'];

    protected $dates = ['punchIn', 'punchOut']; // Carbonインスタンスに変換するフィールドを指定する

    public function user()
    {
        return $this->belongsTo(User::class);
    }

        public function rest()
    {
        return $this->hasMany(Rest::class);
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
