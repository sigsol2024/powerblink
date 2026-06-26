<x-admin.card variant="table" {{ $attributes->merge(['class' => 'overflow-hidden']) }}>
  <div class="overflow-x-auto">
    <table class="pb-admin-table min-w-full text-sm">
      {{ $slot }}
    </table>
  </div>
</x-admin.card>
