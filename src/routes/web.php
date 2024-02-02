<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::middleware('auth')->group(function () {
    Route::get('/', [AuthController::class, 'index']);
});
Route::middleware('auth')->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'attendance']);
});
Route::middleware('auth')->group(function () {
    Route::get('/attendance/{date}', [AttendanceController::class, 'attendanceByDate']);
});

//出退勤打刻
Route::post('/time/timein', [AttendanceController::class, 'timein']);
Route::post('/time/timeout', [AttendanceController::class, 'timeout']);
//休憩打刻
Route::post('/time/breakin', [AttendanceController::class, 'breakin']);
Route::post('/time/breakout', [AttendanceController::class, 'breakout']);