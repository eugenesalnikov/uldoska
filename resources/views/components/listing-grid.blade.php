@props(['listings'])

<div class="grid">
  @forelse ($listings as $listing)
    <x-listing-card :listing="$listing"/>
  @empty
    <x-empty-state
        title="Ничего не найдено"
        text="В этом районе и категории пока пусто."
    />
  @endforelse
</div>
