@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="atte__content">
    <div class="atte__inner">
        <div class="atte__hello">
            <p>さんお疲れ様です！</p>
        </div>
        <div class="atte__button">
            <div class="atte__button-row">
                <button class="atte-start__button">勤務開始</button>
                <button class="atte-stop__button">勤務終了</button>
            </div>
            <div class="atte__button-row">
                <button class="rest-start__button">休憩開始</button>
                <button class="rest-stop__button">休憩終了</button>
            </div>
        </div>
    </div>
    <div class="atte__bottom">
        <small>Atte,inc.</small>
    </div>
</div>
@endsection