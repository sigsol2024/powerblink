<x-app-layout>
  @php
    $staffStats = $staffStats ?? ['total' => $staff->total(), 'admins' => 0, 'editors' => 0];
  @endphp

  <header class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-4 md:px-6 py-3 border-b border-wp-border bg-white sticky top-0 z-40 shrink-0">
    <div class="flex items-center gap-3 min-w-0">
      <h2 class="text-lg font-semibold text-wp-text">{{ __('Admin users') }}</h2>
      <span class="text-xs text-wp-text-muted">{{ trans_choice(':count staff member|:count staff members', $staffStats['total'], ['count' => number_format($staffStats['total'])]) }}</span>
    </div>
    <div class="shrink-0">
      <button type="button" class="admin-luxe-btn-primary" @click="$dispatch('open-staff-create')">
        <x-icon name="plus" class="w-4 h-4" /> {{ __('Add staff') }}
      </button>
    </div>
  </header>

  <div
    class="px-4 md:px-6 py-4 md:py-5 space-y-4"
    x-data="{
      createOpen: {{ $errors->any() && old('_form') === 'create' ? 'true' : 'false' }},
      editOpen: {{ $errors->any() && old('_form') === 'edit' ? 'true' : 'false' }},
      deleteOpen: false,
      editUser: @js(old('_form') === 'edit' ? ['id' => old('user_id'), 'name' => old('name'), 'email' => old('email'), 'role' => old('role'), 'is_super_admin' => (bool) old('is_super_admin')] : null),
      deleteUser: null,
      openEdit(user) { this.editUser = user; this.editOpen = true; },
      openDelete(user) { this.deleteUser = user; this.deleteOpen = true; },
    }"
    @open-staff-create.window="createOpen = true"
    @keydown.escape.window="createOpen = false; editOpen = false; deleteOpen = false"
  >
    @if (session('status'))
      <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">{{ session('status') }}</div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
      <div class="bg-white border border-wp-border rounded p-4">
        <span class="text-xs uppercase tracking-wide text-wp-text-muted">{{ __('Total staff') }}</span>
        <p class="text-2xl font-semibold text-wp-text">{{ number_format($staffStats['total']) }}</p>
      </div>
      <div class="bg-white border border-wp-border rounded p-4">
        <span class="text-xs uppercase tracking-wide text-wp-text-muted">{{ __('Admins') }}</span>
        <p class="text-2xl font-semibold text-wp-text">{{ number_format($staffStats['admins']) }}</p>
      </div>
      <div class="bg-white border border-wp-border rounded p-4">
        <span class="text-xs uppercase tracking-wide text-wp-text-muted">{{ __('Editors') }}</span>
        <p class="text-2xl font-semibold text-wp-text">{{ number_format($staffStats['editors']) }}</p>
      </div>
    </div>

    <div class="overflow-hidden rounded-lg border border-wp-border bg-white">
      <div class="hidden lg:block overflow-x-auto">
        <table class="min-w-full divide-y divide-wp-border text-sm">
          <thead class="bg-wp-bg text-left text-[11px] font-bold uppercase tracking-wider text-wp-text-muted">
            <tr>
              <th class="px-4 py-3">{{ __('Name') }}</th>
              <th class="px-4 py-3">{{ __('Email') }}</th>
              <th class="px-4 py-3">{{ __('Role') }}</th>
              <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-wp-border">
            @foreach ($staff as $member)
              @php
                $roleName = $member->roles->first()?->name ?? '—';
              @endphp
              <tr>
                <td class="px-4 py-3 font-medium text-wp-text">
                  {{ $member->name }}
                  @if ($member->isSuperAdmin())
                    <span class="ml-2 inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-semibold uppercase text-amber-900">{{ __('Super admin') }}</span>
                  @endif
                </td>
                <td class="px-4 py-3 text-wp-text-muted">{{ $member->email }}</td>
                <td class="px-4 py-3">
                  <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold {{ $roleName === 'admin' ? 'bg-violet-100 text-violet-800' : 'bg-sky-100 text-sky-800' }}">
                    {{ $roleName === 'admin' ? __('Admin') : __('Editor') }}
                  </span>
                </td>
                <td class="px-4 py-3 text-right">
                  <div class="inline-flex items-center gap-2">
                    <button type="button" class="admin-btn" @click="openEdit(@js(['id' => $member->id, 'name' => $member->name, 'email' => $member->email, 'role' => $roleName, 'is_super_admin' => $member->isSuperAdmin()]))">{{ __('Edit') }}</button>
                    @if ($member->id !== auth()->id() && ! $member->isSuperAdmin())
                      <button type="button" class="text-xs font-medium text-rose-600 hover:text-rose-500" @click="openDelete(@js(['id' => $member->id, 'name' => $member->name, 'email' => $member->email, 'role' => $roleName]))">{{ __('Delete') }}</button>
                    @endif
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @if ($staff->hasPages())
        <div class="px-4 py-3 admin-luxe-pagination">{{ $staff->links() }}</div>
      @endif
    </div>

    {{-- Create modal --}}
    <div class="fixed inset-0 z-[200] flex items-end justify-center sm:items-center" x-show="createOpen" x-cloak>
      <div class="absolute inset-0 bg-zinc-900/50" @click="createOpen = false"></div>
      <div class="relative z-10 w-full max-w-lg rounded-t-2xl bg-white shadow-2xl sm:rounded-2xl" @click.stop>
        <div class="border-b px-6 py-4">
          <h3 class="text-base font-bold text-zinc-900">{{ __('Add staff account') }}</h3>
        </div>
        <form method="post" action="{{ route('admin.staff.store') }}" class="space-y-4 px-6 py-6">
          @csrf
          <input type="hidden" name="_form" value="create" />
          <div>
            <x-input-label for="create_name" :value="__('Name')" />
            <x-text-input id="create_name" name="name" class="mt-1 block w-full" :value="old('name')" required />
          </div>
          <div>
            <x-input-label for="create_email" :value="__('Email')" />
            <x-text-input id="create_email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required />
          </div>
          <div class="grid gap-4 sm:grid-cols-2">
            <div>
              <x-input-label for="create_password" :value="__('Password')" />
              <x-text-input id="create_password" name="password" type="password" class="mt-1 block w-full" required />
            </div>
            <div>
              <x-input-label for="create_password_confirmation" :value="__('Confirm password')" />
              <x-text-input id="create_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required />
            </div>
          </div>
          <div>
            <x-input-label for="create_role" :value="__('Role')" />
            <select id="create_role" name="role" class="mt-1 block w-full rounded-lg border-zinc-300 text-sm">
              <option value="editor" @selected(old('role') === 'editor')>{{ __('Editor') }}</option>
              <option value="admin" @selected(old('role', 'admin') === 'admin')>{{ __('Admin') }}</option>
            </select>
          </div>
          <div class="flex justify-end gap-2">
            <button type="button" class="admin-btn" @click="createOpen = false">{{ __('Cancel') }}</button>
            <x-primary-button>{{ __('Create account') }}</x-primary-button>
          </div>
        </form>
      </div>
    </div>

    {{-- Edit modal --}}
    <div class="fixed inset-0 z-[200] flex items-end justify-center sm:items-center" x-show="editOpen" x-cloak>
      <div class="absolute inset-0 bg-zinc-900/50" @click="editOpen = false"></div>
      <div class="relative z-10 w-full max-w-lg rounded-t-2xl bg-white shadow-2xl sm:rounded-2xl" @click.stop>
        <div class="border-b px-6 py-4">
          <h3 class="text-base font-bold text-zinc-900">{{ __('Edit staff account') }}</h3>
        </div>
        <form method="post" :action="editUser ? '{{ url('/admin/staff') }}/' + editUser.id : '#'" class="space-y-4 px-6 py-6">
          @csrf
          @method('PUT')
          <input type="hidden" name="_form" value="edit" />
          <input type="hidden" name="user_id" x-bind:value="editUser ? editUser.id : ''" />
          <div>
            <x-input-label for="edit_name" :value="__('Name')" />
            <x-text-input id="edit_name" name="name" class="mt-1 block w-full" x-bind:value="editUser ? editUser.name : ''" required />
          </div>
          <div>
            <x-input-label for="edit_email" :value="__('Email')" />
            <x-text-input id="edit_email" name="email" type="email" class="mt-1 block w-full" x-bind:value="editUser ? editUser.email : ''" required />
          </div>
          <div>
            <x-input-label for="edit_role" :value="__('Role')" />
            <template x-if="editUser && editUser.is_super_admin">
              <input type="hidden" name="role" value="admin" />
            </template>
            <select
              id="edit_role"
              name="role"
              class="mt-1 block w-full rounded-lg border-zinc-300 text-sm"
              x-bind:disabled="editUser && editUser.is_super_admin"
              x-model="editUser ? editUser.role : 'editor'"
            >
              <option value="editor">{{ __('Editor') }}</option>
              <option value="admin">{{ __('Admin') }}</option>
            </select>
            <p class="mt-1 text-xs text-zinc-500" x-show="editUser && editUser.is_super_admin">{{ __('Super admin must remain an admin.') }}</p>
          </div>
          <div class="rounded-lg border border-zinc-200 bg-zinc-50 p-4 space-y-4">
            <div>
              <h4 class="text-sm font-semibold text-zinc-900">{{ __('Change password (optional)') }}</h4>
              <p class="mt-1 text-xs text-zinc-500">{{ __('Leave blank to keep the current password.') }}</p>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
              <div>
                <x-input-label for="edit_password" :value="__('New password')" />
                <x-text-input id="edit_password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
              </div>
              <div>
                <x-input-label for="edit_password_confirmation" :value="__('Confirm new password')" />
                <x-text-input id="edit_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
              </div>
            </div>
          </div>
          <div class="flex justify-end gap-2">
            <button type="button" class="admin-btn" @click="editOpen = false">{{ __('Cancel') }}</button>
            <x-primary-button>{{ __('Save changes') }}</x-primary-button>
          </div>
        </form>
      </div>
    </div>

    {{-- Delete confirmation modal --}}
    <div class="fixed inset-0 z-[200] flex items-end justify-center sm:items-center" x-show="deleteOpen" x-cloak>
      <div class="absolute inset-0 bg-zinc-900/50" @click="deleteOpen = false"></div>
      <div class="relative z-10 w-full max-w-md rounded-t-2xl bg-white shadow-2xl sm:rounded-2xl" @click.stop>
        <div class="border-b px-6 py-4">
          <h3 class="text-base font-bold text-zinc-900">{{ __('Delete staff account?') }}</h3>
        </div>
        <div class="space-y-4 px-6 py-6">
          <p class="text-sm text-zinc-600">{{ __('This permanently removes the staff account and cannot be undone.') }}</p>
          <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900" x-show="deleteUser">
            <p class="font-semibold" x-text="deleteUser ? deleteUser.name : ''"></p>
            <p class="text-rose-800/80" x-text="deleteUser ? deleteUser.email : ''"></p>
            <p class="mt-1 text-xs uppercase tracking-wide text-rose-700" x-text="deleteUser ? deleteUser.role : ''"></p>
          </div>
          <form method="post" :action="deleteUser ? '{{ url('/admin/staff') }}/' + deleteUser.id : '#'" class="flex justify-end gap-2">
            @csrf
            @method('DELETE')
            <button type="button" class="admin-btn" @click="deleteOpen = false">{{ __('Cancel') }}</button>
            <button type="submit" class="inline-flex items-center rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-500">{{ __('Delete account') }}</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
