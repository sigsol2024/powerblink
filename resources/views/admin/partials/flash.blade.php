@if (session('status'))
  <div class="mb-4 rounded-lg bg-secondary/10 border border-secondary/30 p-3 text-sm text-secondary">{{ session('status') }}</div>
@endif
