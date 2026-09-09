@props([
    'title' => 'Модерация',
])

    <!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title }} – Uldoska</title>
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<header class="header">
  <a href="{{ route('moderator.index') }}" class="logo">uldoska / мод</a>
</header>

<main class="page">
  <x-flash/>
  <h1>{{ $title }}</h1>
  {{ $slot }}
</main>
</body>
</html>
