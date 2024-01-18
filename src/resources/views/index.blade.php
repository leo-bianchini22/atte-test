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
                    <button name="work_start" class="atte-start__button">勤務開始</button>
                </form>
                <form action="/time/timeout" method="post">
                    @csrf
                    <button name="work_end" class="atte-stop__button">勤務終了</button>
                </form>
            </div>
            <div class="atte__button-row">
                <form action="/time/breakin" method="post">
                    @csrf
                    <button name="rest_start" class="rest-start__button">休憩開始</button>
                </form>
                <form action="/time/breakout" method="post">
                    @csrf
                    <button name="rest_end" class="rest-stop__button">休憩終了</button>
                </form>
            </div>
        </div>
        <div class="error">
            @error('button')
            {{ $message }}
            @enderror
        </div>
    </div>
</div>
@endsection