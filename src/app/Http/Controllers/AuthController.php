<?php

namespace App\Http\Controllers;

use App\Models\Rest;
use App\Models\Time;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AttendanceRequestRequest;

class AuthController extends Controller
{
    // 打刻画面
    public function index(Request $request)
    {
        $users = Auth::user();
        $times = Time::all();
        $rests = Rest::all();

        // ボタンの表示制御
        $work_clicked = true;
        $rest_clicked = true;
        // ログインユーザーの最新のレコードを取得
        $user = Auth::user();
        $oldtime = Time::where('user_id', $user->id)->latest()->first();
        $oldrest = null;
        if ($oldtime) {
            $oldrest = Rest::where('time_id', $oldtime->id)->latest()->first();
        }
        $today = Carbon::today();
        // 条件付き表示
        if ((empty($oldtime->punchIn)) && (empty($oldtime->punchOut)) && (empty($oldrest->breakIn)) && (empty($oldrest->breakOut))) {
            $work_clicked = true;
            $rest_clicked = true;
            return view('index', compact('times', 'users', 'work_clicked', 'rest_clicked'));
        } elseif (($oldtime->punchIn) && (empty($oldtime->punchOut)) && (empty($oldrest->breakIn)) && (empty($oldrest->breakOut))) {
            $work_clicked = false;
            $rest_clicked = true;
            return view('index', compact('times', 'users', 'work_clicked', 'rest_clicked'));
        } elseif (($oldtime->punchIn) && (empty($oldtime->punchOut)) && ($oldrest->breakIn) && (empty($oldrest->breakOut))) {
            $work_clicked = false;
            $rest_clicked = false;
            return view('index', compact('times', 'users', 'work_clicked', 'rest_clicked'));
        } elseif (($oldtime->punchIn) && (empty($oldtime->punchOut)) && ($oldrest->breakIn) && ($oldrest->breakOut)) {
            $work_clicked = false;
            $rest_clicked = true;
            return view('index', compact('times', 'users', 'work_clicked', 'rest_clicked'));
        }

        return view('index', compact('times', 'users', 'work_clicked', 'rest_clicked'));
    }
}
