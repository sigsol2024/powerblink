<x-app-layout>
  @php
    $userStats = $userStats ?? ['total' => $users->total()];
    $canManageCustomers = $canManageCustomers ?? false;
  @endphp

  <header class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-4 md:px-6 py-3 border-b border-wp-border bg-white sticky top-0 z-40 shrink-0">
    <div class="flex items-center gap-3 min-w-0">
      <h2 class="text-lg font-semibold text-wp-text">{{ __('Customers') }}</h2>
      <span class="text-xs text-wp-text-muted">{{ trans_choice(':count account|:count accounts', $userStats['total'], ['count' => number_format($userStats['total'])]) }}</span>
    </div>
    @if ($canManageCustomers)
      <div class="shrink-0">
        <x-admin.button type="button" @click="$dispatch('open-customer-create')">
          <x-icon name="plus" class="w-4 h-4" /> {{ __('Create customer') }}
        </x-admin.button>
      </div>
    @endif
  </header>

  <div
    class="px-4 md:px-6 py-4 md:py-5 space-y-4"
    x-data="{
      createOpen: {{ $errors->any() && old('_form') === 'create' ? 'true' : 'false' }},
      editOpen: {{ $errors->any() && old('_form') === 'edit' ? 'true' : 'false' }},
      deleteOpen: false,
      editUser: @js(old('_form') === 'edit' ? ['id' => old('user_id'), 'name' => old('name'), 'email' => old('email')] : null),
      deleteUser: null,
      q: '',
      openEdit(user) { this.editUser = user; this.editOpen = true; },
      openDelete(user) { this.deleteUser = user; this.deleteOpen = true; },
    }"
    @open-customer-create.window="createOpen = true"
    @keydown.escape.window="createOpen = false; editOpen = false; deleteOpen = false"
  >
    @if (session('status'))
      <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">{{ session('status') }}</div>
    @endif

    <div class="bg-white border border-wp-border rounded p-4">
      <span class="text-xs uppercase tracking-wide text-wp-text-muted">{{ __('Total customers') }}</span>
      <p class="text-2xl font-semibold text-wp-text">{{ number_format($userStats['total']) }}</p>
    </div>

    <div class="bg-white border border-wp-border rounded p-3 md:p-4">
      <input type="search" x-model.debounce.250ms="q" class="w-full sm:w-72 text-sm" placeholder="{{ __('Search by name or email…') }}" />
    </div>

    @if ($users->total() === 0)
      <p class="text-wp-text-muted py-12 text-center text-sm">{{ __('No customers yet.') }}</p>
    @else
      <div class="bg-white border border-wp-border rounded overflow-hidden">
        <table class="w-full text-left text-sm">
          <thead class="bg-wp-bg text-xs uppercase tracking-wide text-wp-text-muted">
            <tr>
              <th class="px-4 py-3">{{ __('Name') }}</th>
              <th class="px-4 py-3">{{ __('Email') }}</th>
              @if ($canManageCustomers)
                <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
              @endif
            </tr>
          </thead>
          <tbody class="divide-y divide-wp-border">
            @foreach ($users as $user)
              <tr x-show="!q.trim() || @js(strtolower($user->name.' '.$user->email)).includes(q.trim().toLowerCase())">
                <td class="px-4 py-3 font-medium">{{ $user->name }}</td>
                <td class="px-4 py-3 text-wp-text-muted">{{ $user->email }}</td>
                @if ($canManageCustomers)
                  <td class="px-4 py-3 text-right">
                    <div class="inline-flex items-center gap-2">
                      <button type="button" class="admin-btn" @click="openEdit(@js(['id' => $user->id, 'name' => $user->name, 'email' => $user->email]))">{{ __('Edit') }}</button>
                      <button type="button" class="text-xs font-medium text-rose-600 hover:text-rose-500" @click="openDelete(@js(['id' => $user->id, 'name' => $user->name, 'email' => $user->email]))">{{ __('Delete') }}</button>
                    </div>
                  </td>
                @endif
              </tr>
            @endforeach
          </tbody>
        </table>
        @if ($users->hasPages())
          <div class="px-4 py-3">{{ $users->links() }}</div>
        @endif
      </div>
    @endif

    @if ($canManageCustomers)
      <div class="fixed inset-0 z-[200] flex items-end justify-center sm:items-center" x-show="createOpen" x-cloak>
        <div class="absolute inset-0 bg-zinc-900/50" @click="createOpen = false"></div>
        <div class="relative z-10 w-full max-w-lg rounded-t-2xl bg-white shadow-2xl sm:rounded-2xl" @click.stop>
          <div class="border-b px-6 py-4"><h3 class="font-bold">{{ __('Create customer account') }}</h3></div>
          <form method="post" action="{{ route('admin.users.store') }}" class="space-y-4 px-6 py-6">
            @csrf
            <input type="hidden" name="_form" value="create" />
            <div><x-input-label for="create_name" :value="__('Name')" /><x-text-input id="create_name" name="name" class="mt-1 block w-full" :value="old('name')" required /></div>
            <div><x-input-label for="create_email" :value="__('Email')" /><x-text-input id="create_email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required /></div>
            <div class="grid gap-4 sm:grid-cols-2">
              <div><x-input-label for="create_password" :value="__('Password')" /><x-text-input id="create_password" name="password" type="password" class="mt-1 block w-full" required /></div>
              <div><x-input-label for="create_password_confirmation" :value="__('Confirm password')" /><x-text-input id="create_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required /></div>
            </div>
            <div class="flex justify-end gap-2">
              <button type="button" class="admin-btn" @click="createOpen = false">{{ __('Cancel') }}</button>
              <x-primary-button>{{ __('Create account') }}</x-primary-button>
            </div>
          </form>
        </div>
      </div>

      <div class="fixed inset-0 z-[200] flex items-end justify-center sm:items-center" x-show="editOpen" x-cloak>
        <div class="absolute inset-0 bg-zinc-900/50" @click="editOpen = false"></div>
        <div class="relative z-10 w-full max-w-lg rounded-t-2xl bg-white shadow-2xl sm:rounded-2xl" @click.stop>
          <div class="border-b px-6 py-4"><h3 class="font-bold">{{ __('Edit customer account') }}</h3></div>
          <form method="post" :action="editUser ? '{{ url('/admin/users') }}/' + editUser.id : '#'" class="space-y-4 px-6 py-6">
            @csrf
            @method('PUT')
            <input type="hidden" name="_form" value="edit" />
            <input type="hidden" name="user_id" x-bind:value="editUser ? editUser.id : ''" />
            <div><x-input-label for="edit_name" :value="__('Name')" /><x-text-input id="edit_name" name="name" class="mt-1 block w-full" x-bind:value="editUser ? editUser.name : ''" required /></div>
            <div><x-input-label for="edit_email" :value="__('Email')" /><x-text-input id="edit_email" name="email" type="email" class="mt-1 block w-full" x-bind:value="editUser ? editUser.email : ''" required /></div>
            <div class="rounded-lg border border-zinc-200 bg-zinc-50 p-4 space-y-4">
              <div>
                <h4 class="text-sm font-semibold">{{ __('Change password (optional)') }}</h4>
                <p class="mt-1 text-xs text-zinc-500">{{ __('Leave blank to keep the current password.') }}</p>
              </div>
              <div class="grid gap-4 sm:grid-cols-2">
                <div><x-input-label for="edit_password" :value="__('New password')" /><x-text-input id="edit_password" name="password" type="password" class="mt-1 block w-full" /></div>
                <div><x-input-label for="edit_password_confirmation" :value="__('Confirm new password')" /><x-text-input id="edit_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" /></div>
              </div>
            </div>
            <div class="flex justify-end gap-2">
              <button type="button" class="admin-btn" @click="editOpen = false">{{ __('Cancel') }}</button>
              <x-primary-button>{{ __('Save changes') }}</x-primary-button>
            </div>
          </form>
        </div>
      </div>

      <div class="fixed inset-0 z-[200] flex items-end justify-center sm:items-center" x-show="deleteOpen" x-cloak>
        <div class="absolute inset-0 bg-zinc-900/50" @click="deleteOpen = false"></div>
        <div class="relative z-10 w-full max-w-md rounded-t-2xl bg-white shadow-2xl sm:rounded-2xl" @click.stop>
          <div class="border-b px-6 py-4">
            <h3 class="text-base font-bold text-zinc-900">{{ __('Delete customer account?') }}</h3>
          </div>
          <div class="space-y-4 px-6 py-6">
            <p class="text-sm text-zinc-600">{{ __('This permanently removes the customer account and cannot be undone.') }}</p>
            <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900" x-show="deleteUser">
              <p class="font-semibold" x-text="deleteUser ? deleteUser.name : ''"></p>
              <p class="text-rose-800/80" x-text="deleteUser ? deleteUser.email : ''"></p>
            </div>
            <form method="post" :action="deleteUser ? '{{ url('/admin/users') }}/' + deleteUser.id : '#'" class="flex justify-end gap-2">
              @csrf
              @method('DELETE')
              <button type="button" class="admin-btn" @click="deleteOpen = false">{{ __('Cancel') }}</button>
              <button type="submit" class="inline-flex items-center rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-500">{{ __('Delete account') }}</button>
            </form>
          </div>
        </div>
      </div>
    @endif
  </div>
</x-app-layout>
