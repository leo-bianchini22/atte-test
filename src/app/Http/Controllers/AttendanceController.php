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
        $users = User::all();

        return view('attendance', compact('times', 'users',));
    }

    public function search(Request $request)
    {
        $times = Time::with('user')->CreatedSearch($request->created_at)->Paginate(5);
        $users = User::all();

        return view('attendance', compact('times', 'users',));
    }

    //出勤アクション
    public function timein(Request $request)
    {
        // **必要なルール**
        // ・同じ日に2回出勤が押せない(もし打刻されていたらhomeに戻る設定)
        $user = Auth::user();
        $oldtimein = Time::where('user_id', $user->id)->latest()->first(); //一番最新のレコードを取得

        //退勤前に出勤を2度押せない制御
        // if ($oldtimein) {
        //     $oldTimePunchIn = new Carbon($oldtimein->punchIn);
        //     $oldDay = $oldTimePunchIn->startOfDay(); //最後に登録したpunchInの時刻を00:00:00で代入
        // }
        $today = Carbon::today(); //当日の日時を00:00:00で代入

        // if (($oldDay == $today) && (empty($oldtimein->punchOut))) {
        //     return redirect()->back()->with('message', '出勤打刻済みです');
        // }

        // 退勤後に再度出勤を押せない制御
        if ($oldtimein) {
            $oldTimePunchOut = new Carbon($oldtimein->punchOut);
            $oldDay = $oldTimePunchOut->startOfDay(); //最後に登録したpunchInの時刻を00:00:00で代入
        }

        // if (($oldDay == $today)) {
        //     return redirect()->back()->with('message', '退勤打刻済みです');
        // }

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

        // 出勤後に勤務開始ボタンが押せなくなる制御
        $work_clicked = $request->input('work_clicked');
        if ($work_clicked == 'work_start') {
            $work_clicked = true;
        } elseif ($work_clicked == 'work_end') {
            $work_clicked = false;
        }
        // dump($work_clicked);

        return redirect()->route('index')->with('key', $work_clicked);
    }

    //退勤アクション
    public function timeOut(Request $request)
    {
        //ログインユーザーの最新のレコードを取得
        $user = Auth::user();
        $timeOut = Time::where('user_id', $user->id)->latest()->first();

        //string → datetime型
        $now = new Carbon();
        $punchIn = new Carbon($timeOut->punchIn);
        $breakIn = new Carbon($timeOut->breakIn);
        $breakOut = new Carbon($timeOut->breakOut);
        //実労時間(Minute)
        $stayTime = $punchIn->diffInMinutes($now);
        $breakTime = $breakIn->diffInMinutes($breakOut);
        $workingMinute = $stayTime - $breakTime;
        //5分刻み
        $workingHour = ceil($workingMinute / 5) * 5;

        //退勤処理がされていない場合のみ退勤処理を実行
        if ($timeOut) {
            if (empty($timeOut->punchOut)) {
                if ($timeOut->breakIn && !$timeOut->breakOut) {
                    return redirect()->back();
                    // ->with('message', '休憩終了が打刻されていません');
                } else {
                    $timeOut->update([
                        'punchOut' => Carbon::now(),
                        'workTime' => $workingHour
                    ]);
                    return redirect()->back();
                    // ->with('message', 'お疲れ様でした');
                }
            } else {
                $today = new Carbon();
                $day = $today->day;
                $oldPunchOut = new Carbon();
                $oldPunchOutDay = $oldPunchOut->day;
                if ($day == $oldPunchOutDay) {
                    return redirect()->back();
                    // ->with('message', '退勤済みです');
                } else {
                    return redirect()->back();
                    // ->with('message', '出勤打刻をしてください');
                }
            }
        } else {
            return redirect()->back();
            // ->with('message', '出勤打刻がされていません');
        }

        $work_clicked = $request->input('work_clicked');
        if ($work_clicked == 'work_start') {
            $work_clicked = true;
        } elseif ($work_clicked == 'work_end') {
            $work_clicked = false;
        }
        return redirect()->route('index')->with('key', $work_clicked);
    }

    //休憩開始アクション
    public function breakIn(Request $request)
    {
        $user = Time::all();
        $oldtimein = Rest::where('time_id', $user->time_id)->latest()->first();
        // $user = Auth::user();
        // $oldtimein = Time::where('user_id', $user->id)->latest()->first();

        if ($oldtimein->punchIn && !$oldtimein->punchOut && !$oldtimein->breakIn) {
            $oldtimein->update([
                'time_id' => $user->time_id,
                'breakIn' => Carbon::now(),
            ]);
            return redirect()->back();
        }

        $work_clicked = true;
        return redirect()->route('index')->with('key', $work_clicked);
    }

    //休憩終了アクション
    public function breakOut(Request $request)
    {
        $user = Auth::user();
        $oldtimein = Time::where('user_id', $user->id)->latest()->first();

        //string → datetime型
        $now = new Carbon();
        $punchIn = new Carbon($oldtimein->punchIn);
        $punchOut = new Carbon($oldtimein->punchOut);
        $breakIn = new Carbon($oldtimein->breakIn);
        //休憩時間(Minute)
        $stayTime = $punchIn->diffInMinutes($punchOut);
        $breakTime = $breakIn->diffInMinutes($now);
        $breakingMinute = $breakTime + 0;
        //5分刻み
        $breakingHour = ceil($breakingMinute / 5) * 5;

        if ($oldtimein->breakIn && !$oldtimein->breakOut) {
            $oldtimein->update([
                'breakOut' => Carbon::now(),
                'breakTime' => $breakingHour
            ]);
            return redirect()->back();
        }
        return redirect()->back();
    }

    // //勤怠実績
    // public function performance()
    // {
    //     $items = [];
    //     return view('time.performance', ['items' => $items]);
    // }
    // public function result(Request $request)
    // {
    //     $user = Auth::user();
    //     $items = Time::where('user_id', $user->id)->where('year', $request->year)->where('month', $request->month)->get();
    //     return view('time.performance', ['items' => $items]);
    // }
}
