<div {{ $attributes->merge(['class' => 'overflow-x-auto rounded-xl border border-outline-variant/60 bg-surface-container-lowest shadow-sm']) }}>
  <table class="pb-admin-table min-w-full text-sm">
    {{ $slot }}
  </table>
</div>
