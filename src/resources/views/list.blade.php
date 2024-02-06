@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/list.css') }}">
@endsection

@section('content')
<div class="list__content">
    <div>
        <h2>従業員一覧</h2>
    </div>
    <div class="list-table">
        <table class="list-table__inner">
            <tr class="list-table__row__ttl">
                <td>id</td>
                <td>名前</td>
                <td>メールアドレス</td>
                <td></td>
            </tr>
            @foreach($lists as $user)
            <tr class="list-table__row">
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td><a href="{{ route('ListId', ['id'=>$user->id]) }}">詳細</a></td>
            </tr>
            @endforeach
        </table>
    </div>
    <div class="paginate">{{ $lists->render('pagination::bootstrap-4') }}</div>
</div>
@endsection