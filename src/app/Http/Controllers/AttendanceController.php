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
    // 日付一覧画面
    public function attendance()
    {
        $user = Auth::user();
        $oldtime = Time::where('user_id', $user->id)->latest()->first();

        // 現在の日付を取得
        $today = Carbon::today();
        $date = $today->toDateString();

        // 当日の勤怠を取得
        $times = Time::GetMonthAttendance($today->month)->GetDayAttendance($today->day)->paginate(5);
        $rests = Rest::whereDate('created_at', $date)->get();

        return view('attendance', compact('times', 'user', 'rests', 'date'));
    }

    // 日付別一覧画面
    public function attendanceByDate($date)
    {
        $user = Auth::user();
        // Carbonを使って日付を解析し、必要に応じてフォーマットする
        $date = Carbon::parse($date)->toDateString();

        // $dateに対応する勤怠情報を取得
        $times = Time::whereDate('created_at', $date)->paginate(5);
        $rests = Rest::whereDate('created_at', $date)->get();

        return view('attendance', compact('times', 'user', 'rests', 'date'));
    }

    // ユーザー一覧画面
    public function attendanceList()
    {
        $user = Auth::user();
        $lists = User::paginate(5);

        return view('list', compact('lists'));
    }

    public function attendanceListById($id)
    {
        $user = Auth::user();
        $times = Time::where('user_id', $id)->paginate(5);

        return view('listById', compact('times', 'user'));
    }

    //出勤アクション
    public function timein()
    {
        //ログインユーザーの最新のレコードを取得
        $user = Auth::user();
        $latestTimeIn = Time::where('user_id', $user->id)->latest('punchIn')->first();
        $today = Carbon::today();

        if ($latestTimeIn && $latestTimeIn->punchIn->format('Y-m-d') === $today->format('Y-m-d')) {
            return redirect()->back()->with('message', '今日は退勤打刻済みです');
        }

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
            'time_id' => $oldtime->id,
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
        $oldrest = Rest::where('time_id', $oldtime->id)->latest()->first();

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
        $rests = Rest::where('time_id', $oldtime->id)
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

        $oldtime->update([
            'punchOut' => Carbon::now(),
        ]);

        // 勤務時間データ作成
        $punchIn = new Carbon($oldtime->punchIn);
        $punchOut = new Carbon($oldtime->punchOut);
        $totalStayTimeInSeconds = $punchOut->diffInSeconds($punchIn);

        // 休憩時間を取得して差し引く
        $today = Carbon::today();
        $month = intval($today->month);
        $day = intval($today->day);
        $rests = Rest::where('time_id', $oldtime->id)
            ->GetMonthAttendance($month)
            ->GetDayAttendance($day)
            ->get();
        $totalBreakingTimeInSeconds = 0;
        foreach ($rests as $rest) {
            $breakIn = new Carbon($rest->breakIn);
            $breakOut = new Carbon($rest->breakOut);
            $totalBreakingTimeInSeconds += $breakIn->diffInSeconds($breakOut);
        }
        $totalStayTimeInSeconds -= $totalBreakingTimeInSeconds;

        // 勤務時間フォーマット
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
