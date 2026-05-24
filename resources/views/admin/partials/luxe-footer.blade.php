<footer class="w-full py-6 px-margin-mobile md:px-gutter flex flex-col md:flex-row justify-between items-center border-t border-outline-variant bg-surface-container-lowest text-[10px] tracking-[0.2em] font-label-caps text-on-surface-variant shrink-0 {{ $footerClass ?? '' }}">
  <span>© {{ date('Y') }} {{ config('app.name') }}. {{ __('ALL RIGHTS RESERVED.') }}</span>
  <div class="flex flex-wrap gap-6 md:gap-8 mt-4 md:mt-0 justify-center md:justify-end">
    @if (!empty($showPrivacy))
      <a href="{{ route('privacy-policy') }}" class="hover:text-primary transition-colors">{{ __('PRIVACY POLICY') }}</a>
    @endif
    @if (!empty($showTerms))
      <a href="{{ route('terms') }}" class="hover:text-primary transition-colors">{{ __('TERMS OF SERVICE') }}</a>
    @endif
    <a href="{{ route('contact') }}" class="hover:text-primary transition-colors">{{ __('CONTACT US') }}</a>
  </div>
</footer>
