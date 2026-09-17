@props([
    'title' => 'Модерация',
    'heading' => 'Модерация',
])

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex, nofollow">
  <title>{{ $title }}</title>
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="is-mod">
<header class="header">
  <a href="{{ route('moderator.index') }}" class="logo">uldoska / мод</a>
</header>

<main class="page">
  <x-flash/>
  @if ($heading)
    <h1>{{ $heading }}</h1>
  @endif
  {{ $slot }}
</main>
</body>
</html>
