@php
  $image = $image ?? '';
  $eyebrow = $eyebrow ?? '';
  $title = $title ?? '';
  $subtitle = $subtitle ?? '';
  $minHeight = $minHeight ?? 'min-h-[70vh]';
  $primaryCta = $primaryCta ?? null;
  $secondaryCta = $secondaryCta ?? null;
@endphp
<section class="relative {{ $minHeight }} flex items-center overflow-hidden -mt-20 pt-20">
  <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ $image }}')"></div>
  <div class="absolute inset-0 cinematic-overlay" aria-hidden="true"></div>
  <div class="relative z-10 w-full max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop py-16 md:py-24">
    @if ($eyebrow !== '')
      <span class="inline-block bg-secondary-container text-on-secondary-fixed px-4 py-1 rounded-full text-label-caps mb-6 uppercase tracking-widest">{{ $eyebrow }}</span>
    @endif
    @if ($title !== '')
      <h1 class="font-display-hero text-headline-lg-mobile md:text-display-hero text-on-primary mb-6 max-w-4xl">{{ $title }}</h1>
    @endif
    @if ($subtitle !== '')
      <p class="font-body-lg text-on-primary-container max-w-2xl mb-10 leading-relaxed">{{ $subtitle }}</p>
    @endif
    @if ($primaryCta || $secondaryCta)
      <div class="flex flex-wrap gap-4">
        @if ($primaryCta)
          <a href="{{ $primaryCta['href'] ?? '#' }}" class="inline-flex items-center bg-secondary-container text-on-secondary-fixed px-8 py-4 rounded-xl font-headline-md text-sm hover:bg-secondary-fixed transition-all">
            {{ $primaryCta['label'] ?? __('Learn More') }}
          </a>
        @endif
        @if ($secondaryCta)
          <a href="{{ $secondaryCta['href'] ?? '#' }}" class="inline-flex items-center border-2 border-on-primary text-on-primary px-8 py-4 rounded-xl font-headline-md text-sm hover:bg-on-primary hover:text-primary transition-all">
            {{ $secondaryCta['label'] ?? __('Contact') }}
          </a>
        @endif
      </div>
    @endif
  </div>
</section>
