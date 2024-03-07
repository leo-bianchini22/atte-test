@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/list.css') }}">
@endsection

@section('content')
<div class="list__content">
    <div class="list__inner">
        <div class="list__ttl">
            @if ($times->isNotEmpty())
            <p>{{ $times->first()->user->name }}さんの勤怠情報</p>
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
                    <td>{{ $time->user->name }}</td>
                    <td>{{ date('H:i:s', strtotime($time->punchIn)) }}</td>
                    <td>
                        @if($time->punchOut !== null)
                        {{ date('H:i:s', strtotime($time->punchOut)) }}
                        @else
                        打刻してください
                        @endif
                    </td>
                    <td>
                        @if($time->rest->isNotEmpty() && $time->rest->last()->breakTime !== null)
                        {{ date('H:i:s', strtotime($time->rest->last()->breakTime)) }}
                        @elseif($time->rest->isNotEmpty() && $time->rest->last()->breakTime == null)
                        打刻してください
                        @else
                        休憩なし
                        @endif
                    </td>
                    <td>
                        @if($time->punchOut !== null)
                        {{ date('H:i:s', strtotime($time->punchOut)) }}
                        @else
                        打刻してください
                        @endif
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
        <div class="paginate">{{ $times->render('pagination::bootstrap-4') }}</div>
    </div>
</div>
@endsection