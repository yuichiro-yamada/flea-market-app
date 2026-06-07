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
          <input type="search" name="search" value="" placeholder="何をお探しですか？" >
        </div>
      </div>
      <div class="header__right">
        <div class="header__right--menu">ログアウト</div>
        <div class="header__right--menu">マイページ</div>
        <button class="header__button">出品</button>
      </div>
    </div>
  </header>

  <main>
    @yield('content')
  </main>
</body>

</html>
