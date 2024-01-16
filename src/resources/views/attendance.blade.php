@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('content')
<div class="atte__content">
    <div class="paginate__header">ページネーション</div>
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
                <td>{{ $time->punchIn }}</td>
                <td>{{ $time->punchOut }}</td>
                <td>{{ $time->breakTime }}</td>
                <td>{{ $time->workTime }}</td>
            </tr>
            @endforeach
        </table>
    </div>
    <div class="paginate__bottom">ページネーション</div>
</div>
@endsection