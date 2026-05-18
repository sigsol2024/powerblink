$files = @{
  'dashboard\vehicles\index.blade.php' = @'
  <x-slot name="header">
    <h2 class="admin-page-title truncate">
      {{ $isAdminList ? __('All listings') : __('My vehicles') }}
    </h2>
  </x-slot>

  <motion class="admin-content-toolbar">
    <motion class="admin-content-toolbar__actions">
      <a href="{{ route('dashboard.vehicles.create') }}" class="admin-btn-primary">
        {{ __('New listing') }}
      </a>
    </motion>
  </motion>

  <div
'@
}

# Fix motion typos in hashtable - use div
$toolbar = @"
  <motion class="admin-content-toolbar">
    <motion class="admin-content-toolbar__actions">
"@
