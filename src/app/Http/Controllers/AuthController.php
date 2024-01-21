<?php

namespace App\Http\Controllers;

use App\Models\Time;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function index(Request $request)
    {
        $times = Time::all();
        $users = User::all();

        $work_clicked = $request->input('work_clicked');

        return view('index', compact('times', 'users', 'work_clicked'));
    }
}
