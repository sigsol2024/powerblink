<footer class="w-full py-3 px-4 md:px-6 flex flex-col md:flex-row justify-between items-center border-t border-wp-border bg-white text-[11px] text-wp-text-muted shrink-0 {{ $footerClass ?? '' }}">
  <span>© {{ date('Y') }} {{ \App\Support\SiteBrand::displayName() }}</span>
  <div class="flex flex-wrap gap-4 mt-2 md:mt-0 justify-center md:justify-end">
    @if (!empty($showPrivacy))
      <a href="{{ route('privacy-policy') }}" class="hover:text-wp-link transition-colors">{{ __('Privacy policy') }}</a>
    @endif
    @if (!empty($showTerms))
      <a href="{{ route('terms') }}" class="hover:text-wp-link transition-colors">{{ __('Terms of service') }}</a>
    @endif
    <a href="{{ route('contact') }}" class="hover:text-wp-link transition-colors">{{ __('Contact us') }}</a>
  </div>
</footer>
