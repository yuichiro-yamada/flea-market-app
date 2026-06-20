<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Contact Form</title>
  <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/common.css') }}" />
  @yield('css')
</head>

<body>
  <header class="header">
    <div class="header__inner">
      <div class="header__left">
        <a class="header__logo" href="/">
          <img src="/images/header_logo.png">
        </a>
        <div class="header__search">
          <form action="{{ route('index') }}" method="GET">
              @if(request('tab') === 'mylist')
                  <input type="hidden" name="tab" value="mylist">
              @endif
              <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="なにをお探しですか？">
          </form>
        </div>
      </div>


      <form action="/logout" method="post" class="header__right">
        @csrf
        @auth
          <button type="submit" class="header__right--menu header__right--btn">ログアウト</button>
        @endauth
        @guest
          <a href="/login" class="header__right--menu">ログイン</a>
        @endguest


        <a href="/mypage" class="header__right--menu">マイページ</a>
        <a href="/sell" class="header__button">出品</a>
      </form>

    </div>
  </header>

  <main>
    @yield('content')
  </main>
</body>

</html>
