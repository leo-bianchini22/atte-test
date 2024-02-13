@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('content')
<div class="atte__content">
    <div class="atte__inner">
        <div class="atte-form">
            <form class="form" action="/attendance/date" method="post">
                @csrf
                <button type="submit" name="changeDate" value="return">&lt;</button>
                <input type="date" name="form__input-date" value="{{ $date }}" readonly></input>
                <button type="submit" name="changeDate" value="next">&gt;</button>
            </form>
        </div>
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
                    <td>
                        @if($time->rest->isNotEmpty())
                        {{ date('H:i:s', strtotime($time->rest->last()->breakTime)) }}
                        @else
                        休憩なし
                        @endif
                    </td>
                    <td>{{ date('H:i:s', strtotime($time->workTime)) }}</td>
                </tr>
                @endforeach
            </table>
        </div>
        <div class="paginate">{{ $times->render('pagination::bootstrap-4') }}</div>
    </div>
</div>
@endsection