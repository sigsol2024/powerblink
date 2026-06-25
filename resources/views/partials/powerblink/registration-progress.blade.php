@php
  $progressLabels = [
    1 => __('Player'),
    2 => __('Guardian'),
    3 => __('Medical'),
    4 => __('Documents'),
    5 => __('Program'),
    6 => __('Review'),
  ];
@endphp
<nav class="mb-8 md:mb-12" aria-label="{{ __('Registration progress') }}">
  <div class="flex items-center justify-between gap-1 md:gap-2">
    @foreach ($progressLabels as $num => $label)
      @php $active = $step >= $num; $current = $step === $num; @endphp
      <div class="flex flex-col items-center gap-1.5 flex-1 min-w-0">
        <div class="w-9 h-9 md:w-10 md:h-10 rounded-full flex items-center justify-center font-bold text-sm border-2 transition-colors
          {{ $current ? 'bg-primary text-on-primary border-primary' : ($active ? 'bg-secondary text-on-secondary border-secondary' : 'bg-surface-container-high text-on-surface-variant border-outline-variant') }}">
          {{ $num }}
        </div>
        <span class="text-[10px] md:text-label-caps font-label-caps uppercase hidden sm:block truncate w-full text-center {{ $current ? 'text-primary font-bold' : 'text-on-surface-variant' }}">
          {{ $label }}
        </span>
      </div>
      @if ($num < 6)
        <div class="progress-line hidden sm:block {{ $step > $num ? 'bg-secondary' : 'bg-outline-variant' }}" aria-hidden="true"></div>
      @endif
    @endforeach
  </div>
</nav>
