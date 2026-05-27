<x-app-layout>
  <x-slot name="header">
    <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
      <h2 class="text-xl font-semibold text-slate-800">{{ __('Saved listings') }}</h2>
    </div>
  </x-slot>

  <div class="w-full">
    @if (session('status'))
      <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
        {{ session('status') }}
      </div>
    @endif

    <p class="mb-6 text-sm text-slate-600">{{ __('Vehicles you bookmarked from inventory.') }}</p>

    <div class="inv-results" id="listings-result">
      @forelse($vehicles as $vehicle)
        @php
          $images = $vehicle->images ?? collect();
          $hover = $images->slice(0, 5);
        @endphp

        <article class="inv-card">
          <div class="inv-card__media">
            <a href="{{ route('inventory.show', ['slug' => $vehicle->slug]) }}">
              <div class="interactive-hoverable">
                <span class="inv-card__badge">Saved</span>
                <div class="hoverable-wrap">
                  @forelse($hover as $idx => $img)
                    <div class="hoverable-unit {{ $idx === 0 ? 'active' : '' }}">
                      <div class="thumb">
                        <img
                          loading="lazy"
                          decoding="async"
                          src="{{ \App\Support\VehicleImageUrl::url($img->path) }}"
                          alt="{{ $vehicle->title }}"
                        />
                      </div>
                    </div>
                  @empty
                    <div class="hoverable-unit active">
                      <div class="thumb" style="display:flex;align-items:center;justify-content:center;background:#f0f3f7;min-height:220px;">
                        <span class="heading-font text-muted">No image</span>
                      </div>
                    </div>
                  @endforelse
                </div>

                @if($hover->isNotEmpty())
                  <div class="hoverable-indicators">
                    @foreach($hover as $idx => $img)
                      <div class="indicator {{ $idx === 0 ? 'active' : '' }}"></div>
                    @endforeach
                  </div>
                @endif
              </div>
            </a>
          </div>

          <div class="inv-card__body">
            <div class="inv-card__price-row">
              <span class="inv-card__eyebrow">Buy online</span>
              <span class="inv-card__price">
                @if(!is_null($vehicle->price))
                  {{ format_currency($vehicle->price) }}
                @endif
              </span>
            </div>
            <h2 class="inv-card__title">
              <a href="{{ route('inventory.show', ['slug' => $vehicle->slug]) }}">
                {{ $vehicle->title }}
              </a>
            </h2>

            <div class="inv-card__meta">
              <div class="inv-card__meta-item">
                <span class="label">Category</span>
                <span class="value">{{ $vehicle->categoryOption?->value ?? '—' }}</span>
              </div>
            </div>

            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
              <form method="post" action="{{ route('compare.add', ['vehicle' => $vehicle->id]) }}">
                @csrf
                <button type="submit" class="btn btn--primary">Add to compare</button>
              </form>
              <form method="post" action="{{ route('favorites.toggle', ['vehicle' => $vehicle->id]) }}">
                @csrf
                <button type="submit" class="btn btn--outline">Remove</button>
              </form>
            </div>
          </div>
        </article>
      @empty
        <div class="inv-empty">
          <p>No saved vehicles yet.</p>
          <a class="btn btn--primary" href="{{ route('inventory.index') }}" style="margin-top: 12px; display: inline-block;">Browse inventory</a>
        </div>
      @endforelse
    </div>

    @if($vehicles->hasPages())
      <div style="margin: 24px 0 40px;">
        {{ $vehicles->links() }}
      </div>
    @endif
  </div>
</x-app-layout>
