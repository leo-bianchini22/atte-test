@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="atte__content">
    <div class="atte__inner">
        <div class="atte__hello">
            <p>{{ Auth::user()->name }}さんお疲れ様です！</p>
        </div>
        <div class="atte__button">
            <div class="atte__button-row">
                <form action="/time/timein" method="post">
                    @csrf
                    @if(!$work_clicked)
                    <button name="work_clicked" class="atte-start__button" value="work_start">勤務開始</button>
                    @else
                    <button name="work_clicked" class="atte-start__button" value="work_start" disabled>勤務開始</button>
                    @endif
                </form>
                <form action="/time/timeout" method="post">
                    @csrf
                    @if($work_clicked)
                    <button name="work_clicked" class="atte-stop__button" value="work_end">勤務終了</button>
                    @else
                    <button name="work_clicked" class="atte-stop__button" value="work_end" disabled>勤務終了</button>
                    @endif
                </form>
            </div>
            <div class="atte__button-row">
                <form action="/time/breakin" method="post">
                    @csrf
                    <button name="rest_clicked" class="rest-start__button" value="rest_start">休憩開始</button>
                </form>
                <form action="/time/breakout" method="post">
                    @csrf
                    <button name="rest_clicked" class="rest-stop__button" value="rest_end">休憩終了</button>
                </form>
            </div>
        </div>
        <div class="error">
            @error('button')
            {{ $message }} @enderror
        </div>
    </div>
</div>
@endsection