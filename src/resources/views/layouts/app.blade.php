<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Atte</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/common.css') }}" />
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header__content">
            <div class="header__inner">
                <a class="header__logo" href="/">
                    Atte
                </a>
            </div>
            <nav class="header__nav">
                <ul>
                    @if (Auth::check())
                    <li><a href="/">ホーム</a></li>
                    <li><a href="/attendance">日付一覧</a></li>
                    <li><a href="/list">ユーザー一覧</a></li>
                    <form class="logout__form" action="/logout" method="post">
                        @csrf
                        <button>ログアウト</button>
                    </form>
                    @endif
                </ul>
            </nav>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <div class="atte__bottom">
        <small>Atte,inc.</small>
    </div>
</body>

</html>