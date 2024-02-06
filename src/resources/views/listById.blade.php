@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/list.css') }}">
@endsection

@section('content')
<div class="list__content">
    <div>
        @if ($times->isNotEmpty())
        <h2>{{ $times->first()->user->name }}さんの勤怠情報</h2>
        @endif
    </div>
    <div class="list-table">
        <table class="list-table__inner">
            <tr class="list-table__row__ttl">
                <td>日付</td>
                <td>勤務開始</td>
                <td>勤務終了</td>
                <td>休憩時間</td>
                <td>勤務時間</td>
            </tr>
            @foreach($times as $time)
            <tr class="list-table__row">
                <td>{{ $time->year }}/{{ $time->month }}/{{ $time->day }}</td>
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
@endsection