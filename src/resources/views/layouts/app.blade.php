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
                @if (Auth::check())
                <ul>
                    <li>ホーム</li>
                    <li>日付一覧</li>
                    <form class="logout__form" action="/logout" method="post">
                        @csrf
                        <button>ログアウト</button>
                    </form>
                </ul>
                @endif
            </nav>
        </div>
    </header>

    <main>
        @yield('content')
    </main>
</body>

</html>