{{-- listings/index.blade.php --}}
<x-layouts.app :title="$title ?? 'Объявления'">
  <h1>{{ $heading ?? 'Объявления' }}</h1>

  <x-categories :categories="$categories" current="{{ $currentCategorySlug ?? null }}"/>

  <x-listing-grid :listings="$listings"/>

  {{ $listings->links() }}
</x-layouts.app>
