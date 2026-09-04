{{-- components/listing-grid.blade.php --}}
@props(['listings'])

<div class="grid">
  @forelse ($listings as $listing)
    <x-listing-card :listing="$listing"/>
  @empty
    <x-empty-state title="Ничего не найдено" text="Попробуйте другую категорию или снимите фильтры."/>
  @endforelse
</div>
