<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>
    @hasSection('title')
      @yield('title') |
    @endif
    PLC
  </title>

  <link rel="shortcut icon" href="{{ asset('images/logo-dark.png') }}" type="image/x-icon">
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="{{ asset('plugins/fontawesome/css/all.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/app.css') . '?v=' . date('is') }}">

  @vite(['resources/scss/app.scss'])
  @yield('pageStyles')
  <style>
    .login-body {
      background-image: url('{{ asset('images/city-login.jpg') }}');
      background-repeat: no-repeat;
      background-position: center;
      background-size: cover;
    }
  </style>
</head>

<body class="login-body">
  @yield('content')

  @yield('pageScripts')
</body>

</html>
