<?php

namespace App\Http\Controllers;

use App\Models\Time;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function index()
    {
        $times = Time::all();
        $users = User::all();

        return view('index', compact('times', 'users'));
    }
}
