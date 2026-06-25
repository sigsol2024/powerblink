<x-app-layout>
  @php
    $heroImage = \App\Support\PlaceholderMedia::url('asset/images/powerblink/coaching-team-management-powerblink-fc-019.jpg');
    $coachPhoto = $coach?->photo
      ? \App\Support\MediaImageUrl::url($coach->photo->file_path)
      : \App\Support\PlaceholderMedia::url('asset/images/powerblink/player-dashboard-powerblink-fc-063.jpg');
  @endphp

  <x-admin.page-content>
    <section class="relative min-h-[180px] rounded-2xl overflow-hidden mb-6">
      <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ $heroImage }}')"></div>
      <div class="absolute inset-0 bg-gradient-to-r from-primary-container/95 to-primary-container/40"></div>
      <div class="relative px-6 py-8 text-white">
        <h2 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg font-extrabold">
          {{ $coach ? __('Coach :name', ['name' => $coach->name]) : __('Coach dashboard') }}
        </h2>
        @if ($coach)
          <p class="text-white/80 text-sm mt-1">{{ $coach->title }} · {{ $coach->specialization }}</p>
        @endif
      </div>
    </section>

    @if ($coach)
      <section class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        @include('partials.powerblink.dashboard-stat-card', [
          'label' => __('Squad players'),
          'value' => number_format($playerCount ?? 0),
          'accent' => 'secondary',
        ])
        @include('partials.powerblink.dashboard-stat-card', [
          'label' => __('Sessions this week'),
          'value' => number_format($weekSessions ?? 0),
          'accent' => 'navy',
        ])
        @include('partials.powerblink.dashboard-stat-card', [
          'label' => __('Upcoming'),
          'value' => number_format($sessions->count()),
          'hint' => __('scheduled'),
          'accent' => 'gold',
        ])
      </section>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <x-admin.card class="lg:col-span-2 p-6">
          <h3 class="font-headline-md text-headline-md text-primary mb-4">{{ __('Upcoming sessions') }}</h3>
          <div class="overflow-x-auto">
            <table class="pb-admin-table min-w-full">
              <thead>
                <tr>
                  <th>{{ __('Date') }}</th>
                  <th>{{ __('Session') }}</th>
                  <th class="hidden sm:table-cell">{{ __('Program') }}</th>
                  <th class="hidden md:table-cell">{{ __('Location') }}</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($sessions as $session)
                  <tr>
                    <td class="whitespace-nowrap text-on-surface-variant">{{ $session->date?->format('M j') }}</td>
                    <td class="font-medium">{{ $session->title }}</td>
                    <td class="hidden sm:table-cell">{{ $session->program?->name }}</td>
                    <td class="hidden md:table-cell">{{ $session->location }}</td>
                  </tr>
                @empty
                  <tr><td colspan="4" class="text-center py-6 text-on-surface-variant">{{ __('No sessions scheduled.') }}</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </x-admin.card>

        <x-admin.card class="p-6 bg-primary-container text-white">
          <img src="{{ $coachPhoto }}" alt="" class="w-16 h-16 rounded-full object-cover border-2 border-secondary mb-4" />
          <h3 class="font-bold text-lg">{{ $coach->name }}</h3>
          <p class="text-sm text-white/70 mt-1">{{ $coach->license_level ?? __('Licensed coach') }}</p>
          @if ($coach->bio)
            <p class="text-sm text-white/80 mt-4 line-clamp-4">{{ $coach->bio }}</p>
          @endif
        </x-admin.card>
      </div>
    @endif

    @if (($announcements ?? collect())->isNotEmpty())
      <x-admin.card class="mt-4 p-6">
        <h3 class="font-headline-md text-headline-md text-primary mb-4">{{ __('Announcements') }}</h3>
        @foreach ($announcements as $announcement)
          <div class="py-3 border-b border-outline-variant/50 last:border-0">
            <p class="font-semibold">{{ $announcement->title }}</p>
            <p class="text-[10px] text-on-surface-variant mt-1 uppercase">{{ $announcement->published_at?->format('M j, Y') }}</p>
          </div>
        @endforeach
      </x-admin.card>
    @endif
  </x-admin.page-content>
</x-app-layout>
