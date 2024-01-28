<?php

namespace App\Http\Controllers;

use App\Models\Rest;
use App\Models\Time;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpParser\Node\Stmt\Break_;

class AttendanceController extends Controller
{
    public function attendance(Request $request)
    {
        // $today = Carbon::today();
        // $month = intval($today->month);
        // $day = intval($today->day);
        //当日の勤怠を取得
        // $times = Time::GetMonthAttendance($month)->GetDayAttendance($day)->Paginate(5);

        //日付のみ表示
        // $today = date_format($today, 'Y-m-d');

        $times = Time::Paginate(5);
        $rests = Rest::all();
        $users = User::all();

        return view('attendance', compact('times', 'users', 'rests'));
    }

    public function search(Request $request)
    {
        $times = Time::with('user')->CreatedSearch($request->created_at)->Paginate(5);
        $users = User::all();

        return view('attendance', compact('times', 'users',));
    }

    //出勤アクション
    public function timein()
    {
        //ログインユーザーの最新のレコードを取得
        $user = Auth::user();
        $oldtime = Time::where('user_id', $user->id)->latest()->first();

        // 出勤は１日に１度(2回目の出勤ボタン押せない)
        if ($oldtime) {
            $oldTimePunchOut = new Carbon($oldtime->punchOut);
            $oldDay = $oldTimePunchOut->startOfDay(); //最後に登録したpunchInの時刻を00:00:00で代入
        }

        $today = Carbon::today();

        // if (($oldDay == $today)) {
        //     return redirect()->back()->with('message', '退勤打刻済みです');
        // }

        // 勤務開始データ作成
        $month = intval($today->month);
        $day = intval($today->day);
        $year = intval($today->year);

        $time = Time::create([
            'user_id' => $user->id,
            'punchIn' => Carbon::now(),
            'month' => $month,
            'day' => $day,
            'year' => $year,
        ]);

        return redirect()->back();
    }

    //休憩開始アクション
    public function breakIn()
    {
        //ログインユーザーの最新のレコードを取得
        $user = Auth::user();
        $oldtime = Time::where('user_id', $user->id)->latest()->first();
        $oldrest = Rest::where('time_id', $oldtime->id)->latest()->first();

        // 休憩開始データ作成
        $today = Carbon::today();
        $month = intval($today->month);
        $day = intval($today->day);
        $year = intval($today->year);

        $rest = Rest::create([
            'time_id' => $user->id,
            'breakIn' => Carbon::now(),
            'month' => $month,
            'day' => $day,
            'year' => $year,
        ]);

        return redirect()->back();
    }

    //休憩終了アクション
    public function breakOut()
    {
        //ログインユーザーの最新のレコードを取得
        $user = Auth::user();
        $oldtime = Time::where('user_id', $user->id)->latest()->first();
        $oldrest = Rest::where('time_id', $user->id)->latest()->first();

        // 休憩終了データ作成
        if ($oldrest->breakIn && !$oldrest->breakOut) {
            $oldrest->update([
                'breakOut' => Carbon::now(),
            ]);
        }

        // 休憩時間データ作成
        $today = Carbon::today();
        $month = intval($today->month);
        $day = intval($today->day);
        $rests = Rest::where('time_id', $user->id)
            ->GetMonthAttendance($month)
            ->GetDayAttendance($day)
            ->get();

        $totalBreakingTimeInSeconds = 0; // トータル休憩時間の秒数リセット

        foreach ($rests as $rest) {
            //string → datetime型
            $breakIn = new Carbon($rest->breakIn);
            $breakOut = new Carbon($rest->breakOut);

            $totalBreakingTimeInSeconds += $breakIn->diffInSeconds($breakOut);
        }

        $breakingTimeHours = floor($totalBreakingTimeInSeconds / 3600);
        $breakingTimeMinutes = floor($totalBreakingTimeInSeconds / 60 - $breakingTimeHours * 60);
        $breakingTimeSeconds = floor($totalBreakingTimeInSeconds % 60);
        $totalBreakingTime = sprintf('%02d:%02d:%02d', $breakingTimeHours, $breakingTimeMinutes, $breakingTimeSeconds);

        $oldrest->update([
            'breakTime' => Carbon::create($totalBreakingTime)
        ]);


        return redirect()->back();
    }

    //退勤アクション
    public function timeOut(Request $request)
    {
        // ログインユーザーの最新のレコードを取得
        $user = Auth::user();
        $oldtime = Time::where('user_id', $user->id)->latest()->first();
        $oldrest = Rest::where('time_id', $oldtime->id)->latest()->first();

        //退勤処理がされていない場合のみ退勤処理を実行
        if ($oldtime) {
            if (empty($oldtime->punchOut)) {
                if ($oldtime->breakIn && !$oldtime->breakOut) {
                    return redirect()->back()->with('message', '休憩終了を打刻してください');
                } else {
                    $oldtime->update([
                        'punchOut' => Carbon::now(),
                    ]);
                }
            }
        }

        // 勤務時間を計算
        $punchIn = new Carbon($oldtime->punchIn);
        $punchOut = new Carbon($oldtime->punchOut);
        $totalStayTimeInSeconds = $punchOut->diffInSeconds($punchIn);

        // 休憩時間を取得して差し引く
        $today = Carbon::today();
        $month = intval($today->month);
        $day = intval($today->day);
        $rests = Rest::where('time_id', $user->id)
            ->GetMonthAttendance($month)
            ->GetDayAttendance($day)
            ->get();
        $totalBreakingTimeInSeconds = 0; // トータル休憩時間の秒数リセット
        foreach ($rests as $rest) {
            $breakIn = new Carbon($rest->breakIn);
            $breakOut = new Carbon($rest->breakOut);
            $totalBreakingTimeInSeconds += $breakIn->diffInSeconds($breakOut); // 休憩時間を取得
        }
        $totalStayTimeInSeconds -= $totalBreakingTimeInSeconds; // 休憩時間を勤務時間から引く

        // 勤務時間をフォーマット
        $workingTimeHours = floor($totalStayTimeInSeconds / 3600);
        $workingTimeMinutes = floor(($totalStayTimeInSeconds % 3600) / 60);
        $workingTimeSeconds = $totalStayTimeInSeconds % 60;
        $totalworkingTime = sprintf('%02d:%02d:%02d', $workingTimeHours, $workingTimeMinutes, $workingTimeSeconds);


        $oldtime->update([
            'workTime' => $totalworkingTime
        ]);

        return redirect()->back();
    }
}
