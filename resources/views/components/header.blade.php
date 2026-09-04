<header class="header">
  <a href="{{ route('home') }}" class="logo">uldoska</a>

  <form class="search" action="{{ route('listings.index') }}" method="get" role="search">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Найти объявление" enterkeyhint="search">
    <button type="submit">Найти</button>
  </form>
</header>
