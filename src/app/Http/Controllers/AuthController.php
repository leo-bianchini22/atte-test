<?php

namespace App\Http\Controllers;

use App\Models\Rest;
use App\Models\Time;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index(Request $request)
    {
        $users = Auth::user();

        $times = Time::where('user_id', $users->id)->latest()->first();
        $rests = Rest::where('time_id', $users->time_id)->latest()->first();

        $work_clicked = $request->session()->get('key');

        return view('index', compact('times', 'users', 'work_clicked'));
    }
}
