@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('content')
<div class="atte__content">
    <form class="search__form" action="/attendance/search" method="get">
        @csrf
        <!-- <button class="search__button-left">&lt;</button>
        <div class="search-form__date"></div>
        <button class="search__button-right">&gt;</button> -->
        <input type="date" class="search-form__date" name="created_at"><button class="search__button-right">&gt;</button>
    </form>
    <div class="atte-table">
        <table class="atte-table__inner">
            <tr class="atte-table__row__ttl">
                <td>名前</td>
                <td>勤務開始</td>
                <td>勤務終了</td>
                <td>休憩時間</td>
                <td>勤務時間</td>
            </tr>
            @foreach($times as $time)
            <tr class="atte-table__row">
                <td>{{ $time->user->name }}</td>
                <td>{{ date('H:i:s', strtotime($time->punchIn)) }}</td>
                <td>{{ date('H:i:s', strtotime($time->punchOut)) }}</td>
                <td>{{ $time->breakTime }}</td>
                <td>{{ $time->workTime }}</td>
            </tr>
            @endforeach
        </table>
    </div>
    <div class="paginate">{{ $times->render('pagination::bootstrap-4') }}</div>
</div>
@endsection