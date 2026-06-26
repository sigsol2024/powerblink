<x-app-layout>
  @php
    $heroImage = \App\Support\PlaceholderMedia::url('asset/images/powerblink/player-dashboard-powerblink-fc-061.jpg');
    $playerPhoto = $player?->photo
      ? \App\Support\MediaImageUrl::url($player->photo->file_path)
      : \App\Support\PlaceholderMedia::url('asset/images/powerblink/player-dashboard-powerblink-fc-060.jpg');
  @endphp

  <x-admin.page-content>
    @if ($player)
      <section class="relative min-h-[220px] md:min-h-[280px] rounded-2xl overflow-hidden mb-6 group">
        <div class="absolute inset-0 bg-cover bg-center transition-transform duration-700 group-hover:scale-105" style="background-image: url('{{ $heroImage }}')"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-primary-container via-primary-container/70 to-transparent"></div>
        <div class="relative h-full flex flex-col justify-center px-6 md:px-10 py-8 text-white min-h-[220px] md:min-h-[280px]">
          <h2 class="font-display-hero text-3xl md:text-5xl font-extrabold mb-2">{{ __('Welcome back, :name', ['name' => explode(' ', $player->name)[0]]) }}</h2>
          <p class="text-body-lg italic text-secondary-fixed max-w-xl mb-4 text-sm md:text-base">{{ $player->program?->name ?? __('Academy player') }}</p>
          <div class="flex flex-wrap items-center gap-3">
            <span class="px-4 py-1.5 bg-secondary rounded-full font-label-caps text-label-caps text-xs uppercase">{{ ucfirst($player->status) }}</span>
            @if ($nextSession ?? null)
              <span class="px-4 py-1.5 bg-white/20 backdrop-blur-sm rounded-full font-label-caps text-label-caps text-xs uppercase">
                {{ __('Next: :date', ['date' => $nextSession->date?->format('M j')]) }} · {{ $nextSession->title }}
              </span>
            @endif
          </div>
        </div>
      </section>

      <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        @include('partials.powerblink.dashboard-stat-card', [
          'label' => __('Attendance rate'),
          'value' => $attendanceRate !== null ? $attendanceRate.'%' : '—',
          'accent' => 'secondary',
        ])
        @include('partials.powerblink.dashboard-stat-card', [
          'label' => __('Program'),
          'value' => $player->program?->name ?? '—',
          'accent' => 'navy',
        ])
        @include('partials.powerblink.dashboard-stat-card', [
          'label' => __('Position'),
          'value' => $player->primary_position ?? '—',
          'accent' => 'gold',
        ])
        @include('partials.powerblink.dashboard-stat-card', [
          'label' => __('Sessions this month'),
          'value' => number_format($monthSessions ?? 0),
          'accent' => 'green',
        ])
      </section>

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
        <x-admin.card class="lg:col-span-7 p-6">
          <h3 class="font-headline-md text-headline-md text-primary mb-4">{{ __('Upcoming sessions') }}</h3>
          <div class="space-y-4">
            @forelse ($upcomingSessions as $session)
              <div class="flex gap-4 items-start border-l-2 border-secondary pl-4 py-1">
                <div>
                  <p class="font-label-caps text-[10px] text-secondary uppercase">{{ $session->date?->format('D · M j') }} @if($session->start_time) · {{ $session->start_time }} @endif</p>
                  <h4 class="font-bold">{{ $session->title }}</h4>
                  <p class="text-xs text-on-surface-variant">{{ $session->location }}</p>
                </div>
              </div>
            @empty
              <p class="text-sm text-on-surface-variant">{{ __('No upcoming sessions scheduled.') }}</p>
            @endforelse
          </div>
        </x-admin.card>

        <x-admin.card class="lg:col-span-5 p-6 bg-primary-container text-white">
          <h3 class="font-headline-md text-headline-md mb-4">{{ __('Player profile') }}</h3>
          <div class="flex items-center gap-4 mb-4">
            <img src="{{ $playerPhoto }}" alt="" class="w-14 h-14 rounded-full object-cover border-2 border-secondary" />
            <div>
              <p class="font-bold">{{ $player->name }}</p>
              <p class="text-xs text-white/70">{{ $player->player_code ?? __('No code assigned') }}</p>
            </div>
          </div>
          <dl class="grid grid-cols-2 gap-3 text-sm">
            <div><dt class="text-white/60 text-xs">{{ __('Season') }}</dt><dd class="font-medium">{{ $player->season?->name ?? '—' }}</dd></div>
            <div><dt class="text-white/60 text-xs">{{ __('Secondary') }}</dt><dd class="font-medium">{{ $player->secondary_position ?? '—' }}</dd></div>
          </dl>
        </x-admin.card>
      </div>

      <x-admin.card class="mt-4 p-6">
        <h3 class="font-headline-md text-headline-md text-primary mb-4">{{ __('Recent attendance') }}</h3>
        <div class="overflow-x-auto">
          <table class="pb-admin-table min-w-full">
            <thead><tr><th>{{ __('Session') }}</th><th class="text-right">{{ __('Status') }}</th></tr></thead>
            <tbody>
              @forelse ($attendance as $record)
                <tr>
                  <td>{{ $record->trainingSession?->title ?? '—' }}</td>
                  <td class="text-right">
                    <x-admin.status-pill :variant="$record->status === 'present' ? 'activated' : 'neutral'">{{ $record->status }}</x-admin.status-pill>
                  </td>
                </tr>
              @empty
                <tr><td colspan="2" class="text-center text-on-surface-variant py-6">{{ __('No attendance records yet.') }}</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </x-admin.card>

      @if (($announcements ?? collect())->isNotEmpty())
        <x-admin.card class="mt-4 p-6">
          <h3 class="font-headline-md text-headline-md text-primary mb-4">{{ __('Announcements') }}</h3>
          @foreach ($announcements as $announcement)
            <div class="py-3 border-b border-outline-variant/50 last:border-0">
              <p class="font-semibold">{{ $announcement->title }}</p>
              <p class="text-sm text-on-surface-variant line-clamp-2 mt-1">{{ $announcement->body }}</p>
            </div>
          @endforeach
        </x-admin.card>
      @endif
    @else
      <x-admin.card class="p-8 text-center">
        <x-icon name="person_off" class="w-10 h-10 text-on-surface-variant mb-3" />
        <p class="text-on-surface-variant">{{ __('Player profile not linked yet.') }}</p>
      </x-admin.card>
    @endif
  </x-admin.page-content>
</x-app-layout>
