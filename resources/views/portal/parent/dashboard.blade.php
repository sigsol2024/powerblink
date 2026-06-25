<x-app-layout>
  @php
    $user = auth()->user();
    $heroImage = \App\Support\PlaceholderMedia::url('asset/images/powerblink/player-dashboard-powerblink-fc-061.jpg');
  @endphp

  <x-admin.page-content>
    <section class="relative min-h-[180px] rounded-2xl overflow-hidden mb-6">
      <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ $heroImage }}')"></div>
      <div class="absolute inset-0 bg-gradient-to-r from-primary-container/95 to-primary-container/50"></div>
      <div class="relative px-6 py-8 text-white">
        <h2 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg font-extrabold">{{ __('Welcome, :name', ['name' => explode(' ', $user->name)[0]]) }}</h2>
        <p class="text-white/80 text-sm mt-1">{{ __('Parent portal — manage registrations and follow your athletes.') }}</p>
      </div>
    </section>

    <section class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
      @include('partials.powerblink.dashboard-stat-card', [
        'label' => __('Linked players'),
        'value' => number_format($players->count()),
        'accent' => 'secondary',
      ])
      @include('partials.powerblink.dashboard-stat-card', [
        'label' => __('Open applications'),
        'value' => number_format($pendingRegistrations ?? 0),
        'accent' => 'gold',
      ])
      @include('partials.powerblink.dashboard-stat-card', [
        'label' => __('Total registrations'),
        'value' => number_format($registrations->count()),
        'accent' => 'navy',
      ])
    </section>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
      <x-admin.card class="p-6">
        <h3 class="font-headline-md text-headline-md text-primary mb-4">{{ __('My children') }}</h3>
        @forelse ($players as $player)
          <div class="py-3 border-b border-outline-variant/50 last:border-0 flex justify-between gap-3 items-start">
            <div>
              <p class="font-semibold">{{ $player->name }}</p>
              <p class="text-xs text-on-surface-variant">{{ $player->program?->name }} · {{ ucfirst($player->status) }}</p>
            </div>
            <x-admin.status-pill :variant="$player->status === 'active' ? 'activated' : 'neutral'">{{ $player->status }}</x-admin.status-pill>
          </div>
        @empty
          <p class="text-sm text-on-surface-variant">{{ __('No linked players yet.') }}</p>
        @endforelse
      </x-admin.card>

      <x-admin.card class="p-6">
        <h3 class="font-headline-md text-headline-md text-primary mb-4">{{ __('Recent registrations') }}</h3>
        @forelse ($registrations as $registration)
          <div class="py-3 border-b border-outline-variant/50 last:border-0">
            <div class="flex justify-between gap-2 items-start">
              <div>
                <p class="font-semibold">{{ $registration->player_name }}</p>
                <p class="text-xs text-on-surface-variant">{{ $registration->program?->name }}</p>
              </div>
              <x-admin.status-pill :variant="$registration->status">{{ str_replace('_', ' ', $registration->status) }}</x-admin.status-pill>
            </div>
          </div>
        @empty
          <p class="text-sm text-on-surface-variant">{{ __('No registrations.') }}</p>
        @endforelse
      </x-admin.card>
    </div>

    @if ($announcements->isNotEmpty())
      <x-admin.card class="mt-4 p-6">
        <h3 class="font-headline-md text-headline-md text-primary mb-4">{{ __('Announcements') }}</h3>
        @foreach ($announcements as $announcement)
          <div class="py-3 border-b border-outline-variant/50 last:border-0">
            <p class="font-semibold">{{ $announcement->title }}</p>
            <p class="text-sm text-on-surface-variant line-clamp-2 mt-1">{{ $announcement->body }}</p>
            <p class="text-[10px] text-on-surface-variant mt-2 uppercase tracking-wide">{{ $announcement->published_at?->diffForHumans() }}</p>
          </div>
        @endforeach
      </x-admin.card>
    @endif

    <div class="mt-6">
      <x-admin.button variant="primary" :href="route('registration.wizard')">{{ __('Start new registration') }}</x-admin.button>
    </div>
  </x-admin.page-content>
</x-app-layout>
