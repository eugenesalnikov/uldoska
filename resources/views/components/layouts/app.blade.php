@props([
    'title' => 'Uldoska – объявления Ульяновска',
    'description' => 'Доска объявлений города Ульяновска',
    'heading' => null,
    'category' => null,
    'robots' => null,
])
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @if ($robots)
    <meta name="robots" content="{{ $robots }}">
  @endif
  {{ $head ?? '' }}
  <title>{{ $title }}</title>
  <meta name="description" content="{{ $description }}">
@stack('styles')
  <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
  <script src="{{ asset('js/app.js') }}?v={{ filemtime(public_path('js/app.js')) }} " defer></script>
</head>
<body>
<x-header :category="$category"/>
<main class="page">
  <x-flash/>
  @if ($heading)
    <h1>{{ $heading }}</h1>
  @endif
  {{ $slot }}
</main>
<x-footer/>
@stack('scripts')
</body>
</html>
