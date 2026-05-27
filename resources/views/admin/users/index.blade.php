<x-app-layout>
  @php
    $userStats = $userStats ?? ['total' => $users->total(), 'admins' => 0, 'customers' => 0];
  @endphp

  <header class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-4 md:px-6 py-3 border-b border-wp-border bg-white sticky top-0 z-40 shrink-0">
    <div class="flex items-center gap-3 min-w-0">
      <h2 class="text-lg font-semibold text-wp-text">{{ __('Customers') }}</h2>
      <span class="text-xs text-wp-text-muted">{{ trans_choice(':count account|:count accounts', $userStats['total'], ['count' => number_format($userStats['total'])]) }}</span>
    </div>
    <div class="shrink-0">
      <button type="button" class="admin-luxe-btn-primary" @click="$dispatch('open-invite-modal')">
        <x-icon name="plus" class="w-4 h-4" /> {{ __('Invite or create account') }}
      </button>
    </div>
  </header>

  <div
    class="px-4 md:px-6 py-4 md:py-5 space-y-4"
    x-data="{
      inviteOpen: {{ $errors->any() ? 'true' : 'false' }},
      openId: null,
      expandedMobileId: null,
      q: '',
      roleFilter: 'all',
      toggleOpen(id) { this.openId = this.openId === id ? null : id; },
      toggleMobile(id) { this.expandedMobileId = this.expandedMobileId === id ? null : id; },
      visible(roleNames, haystack) {
        const query = this.q.trim().toLowerCase();
        const searchOk = !query || haystack.toLowerCase().includes(query);
        let roleOk = true;
        if (this.roleFilter === 'admin') roleOk = roleNames.includes('admin');
        else if (this.roleFilter === 'customer') roleOk = !roleNames.includes('admin');
        return searchOk && roleOk;
      },
    }"
    @open-invite-modal.window="inviteOpen = true"
    @keydown.escape.window="inviteOpen = false"
  >
    {{-- Stats strip --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
      <div class="bg-white border border-wp-border rounded p-4 flex flex-col justify-between min-h-[5.5rem]">
        <span class="text-xs uppercase tracking-wide text-wp-text-muted">{{ __('Total accounts') }}</span>
        <span class="text-2xl font-semibold text-wp-text leading-none">{{ number_format($userStats['total']) }}</span>
      </div>
      <div class="bg-white border border-wp-border rounded p-4 flex flex-col justify-between min-h-[5.5rem]">
        <span class="text-xs uppercase tracking-wide text-wp-text-muted">{{ __('Admins') }}</span>
        <span class="text-2xl font-semibold text-wp-text leading-none">{{ number_format($userStats['admins']) }}</span>
      </div>
      <div class="bg-white border border-wp-border rounded p-4 flex flex-col justify-between min-h-[5.5rem]">
        <span class="text-xs uppercase tracking-wide text-wp-text-muted">{{ __('Customers') }}</span>
        <span class="text-2xl font-semibold text-wp-text leading-none">{{ number_format($userStats['customers']) }}</span>
      </div>
    </div>

    {{-- Toolbar: search + role filter --}}
    <div class="bg-white border border-wp-border rounded p-3 md:p-4 flex flex-col gap-3">
      <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
        <div class="relative flex-1 sm:flex-initial">
          <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-wp-text-muted pointer-events-none">
            <x-icon name="search" class="w-4 h-4" />
          </span>
          <input
            type="search"
            x-model.debounce.250ms="q"
            class="w-full sm:w-72 pl-9 pr-3 text-sm"
            placeholder="{{ __('Search by name or email…') }}"
            aria-label="{{ __('Search customers') }}"
          />
        </div>
        <button
          type="button"
          x-show="q.length > 0"
          x-cloak
          @click="q = ''"
          class="text-xs text-wp-text-muted hover:text-wp-text underline"
        >{{ __('Clear') }}</button>
      </div>
      <div class="flex flex-wrap items-center gap-1.5">
        <span class="text-[11px] text-wp-text-muted mr-1">{{ __('Filter') }}:</span>
        @foreach (['all' => __('All'), 'customer' => __('Customer'), 'admin' => __('Admin')] as $key => $label)
          <button
            type="button"
            @click="roleFilter = '{{ $key }}'"
            :class="roleFilter === '{{ $key }}' ? 'bg-wp-link text-white border-wp-link' : 'border-wp-border bg-white hover:bg-wp-bg text-wp-text'"
            class="border px-2.5 py-1 text-xs rounded"
          >{{ $label }}</button>
        @endforeach
      </div>
    </div>

    @if ($users->total() === 0)
      <p class="text-wp-text-muted py-12 text-center text-sm">{{ __('No customers yet.') }}</p>
    @else
      {{-- Desktop table --}}
      <div class="hidden lg:block bg-white border border-wp-border rounded overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse text-sm">
            <thead>
              <tr class="bg-wp-bg">
                <th class="px-4 py-2.5 text-xs uppercase tracking-wide text-wp-text-muted border-b border-wp-border">{{ __('Name') }}</th>
                <th class="px-4 py-2.5 text-xs uppercase tracking-wide text-wp-text-muted border-b border-wp-border">{{ __('Email') }}</th>
                <th class="px-4 py-2.5 text-xs uppercase tracking-wide text-wp-text-muted border-b border-wp-border">{{ __('Roles') }}</th>
                <th class="px-4 py-2.5 text-xs uppercase tracking-wide text-wp-text-muted border-b border-wp-border text-right">{{ __('Actions') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach($users as $user)
                @php
                  $roleNames = $user->roles->pluck('name')->all();
                  $haystack = strtolower(trim($user->name.' '.$user->email));
                @endphp
                <tr class="hover:bg-wp-bg/40 transition-colors" x-show="visible(@js($roleNames), @js($haystack))">
                  <td class="px-4 py-3 border-b border-wp-border font-medium text-wp-text">{{ $user->name }}</td>
                  <td class="px-4 py-3 border-b border-wp-border text-wp-text-muted">{{ $user->email }}</td>
                  <td class="px-4 py-3 border-b border-wp-border">
                    <div class="flex flex-wrap gap-1">
                      @foreach($user->roles as $role)
                        @if($role->name === 'admin')
                          <span class="inline-flex rounded bg-violet-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-violet-800 border border-violet-200">{{ __('Admin') }}</span>
                        @elseif($role->name === 'user')
                          <span class="inline-flex rounded bg-sky-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-sky-800 border border-sky-200">{{ __('Customer') }}</span>
                        @else
                          <span class="inline-flex rounded bg-zinc-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-zinc-700 border border-zinc-200">{{ $role->name }}</span>
                        @endif
                      @endforeach
                    </div>
                  </td>
                  <td class="px-4 py-3 border-b border-wp-border text-right">
                    @if($user->id === auth()->id())
                      <span class="text-xs text-wp-text-muted">{{ __('You') }}</span>
                    @else
                      <form method="post" action="{{ route('admin.users.destroy', $user) }}" class="inline" onsubmit="return confirm(@json(__('Delete this user?')));">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs font-medium text-rose-700 hover:underline inline-flex items-center gap-1">
                          <x-icon name="trash" class="w-3.5 h-3.5" /> {{ __('Delete') }}
                        </button>
                      </form>
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        @if ($users->hasPages())
          <div class="px-4 py-3 border-t border-wp-border admin-luxe-pagination">{{ $users->links() }}</div>
        @endif
      </div>

      {{-- Mobile accordion --}}
      <div class="lg:hidden space-y-2">
        @foreach ($users as $user)
          @php
            $roleNames = $user->roles->pluck('name')->all();
            $haystack = strtolower(trim($user->name.' '.$user->email));
            $isAdmin = in_array('admin', $roleNames, true);
          @endphp
          <div class="bg-white border border-wp-border rounded overflow-hidden" x-show="visible(@js($roleNames), @js($haystack))">
            <button
              type="button"
              class="w-full flex items-center gap-3 p-3 text-left"
              @click="toggleMobile({{ $user->id }})"
              :aria-expanded="expandedMobileId === {{ $user->id }} ? 'true' : 'false'"
            >
              <div class="w-10 h-10 bg-wp-bg rounded-full flex items-center justify-center font-bold text-xs shrink-0">
                {{ strtoupper(substr($user->name, 0, 1)) }}
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between gap-2">
                  <span class="font-medium text-wp-text text-sm truncate">{{ $user->name }}</span>
                  @if($isAdmin)
                    <span class="inline-flex rounded bg-violet-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-violet-800 border border-violet-200 shrink-0">{{ __('Admin') }}</span>
                  @else
                    <span class="inline-flex rounded bg-sky-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-sky-800 border border-sky-200 shrink-0">{{ __('Customer') }}</span>
                  @endif
                </div>
                <p class="text-xs text-wp-text-muted truncate mt-0.5">{{ $user->email }}</p>
              </div>
              <svg :class="expandedMobileId === {{ $user->id }} ? 'rotate-180' : ''" class="w-4 h-4 transition-transform text-wp-text-muted shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
            <div
              x-show="expandedMobileId === {{ $user->id }}"
              x-cloak
              x-transition.duration.150ms
              class="border-t border-wp-border bg-wp-bg/40 p-3 space-y-2 text-sm"
            >
              <div class="flex justify-between gap-3">
                <span class="text-xs text-wp-text-muted">{{ __('Email') }}</span>
                <span class="text-wp-text text-right truncate">{{ $user->email }}</span>
              </div>
              <div class="flex justify-between gap-3">
                <span class="text-xs text-wp-text-muted">{{ __('Joined') }}</span>
                <span class="text-wp-text text-right">{{ $user->created_at?->format('M j, Y') ?? '—' }}</span>
              </div>
              <div class="pt-2">
                @if($user->id === auth()->id())
                  <p class="text-center text-xs text-wp-text-muted">{{ __('This is your account.') }}</p>
                @else
                  <form method="post" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm(@json(__('Delete this user?')));">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="block w-full text-center bg-rose-600 text-white hover:bg-rose-500 px-3 py-2 text-xs font-medium rounded transition-colors">
                      {{ __('Delete account') }}
                    </button>
                  </form>
                @endif
              </div>
            </div>
          </div>
        @endforeach

        @if ($users->hasPages())
          <div class="py-2 admin-luxe-pagination">{{ $users->links() }}</div>
        @endif
      </div>
    @endif

    {{-- Modal: create / invite user --}}
    <div
      class="fixed inset-0 z-[200] flex items-end justify-center sm:items-center"
      x-show="inviteOpen"
      x-cloak
      x-transition.opacity.duration.200ms
      aria-modal="true"
      role="dialog"
    >
      <div class="absolute inset-0 bg-zinc-900/50 backdrop-blur-[1px]" @click="inviteOpen = false" aria-hidden="true"></div>
      <div
        class="relative z-10 mb-0 max-h-[90dvh] w-full max-w-lg overflow-y-auto rounded-t-2xl border border-zinc-200 bg-white shadow-2xl sm:m-4 sm:rounded-2xl"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        @click.stop
      >
        <div class="sticky top-0 flex items-start justify-between gap-4 border-b border-zinc-100 bg-white px-6 py-4">
          <div>
            <h3 class="text-base font-bold text-zinc-900">{{ __('Invite or create an account') }}</h3>
            <p class="mt-1 text-sm text-zinc-600">{{ __('Choose Admin for full console access, or Customer for shoppers.') }}</p>
          </div>
          <button type="button" class="rounded-lg p-2 text-zinc-500 transition hover:bg-zinc-100 hover:text-zinc-800" @click="inviteOpen = false" aria-label="{{ __('Close') }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
        <form method="post" action="{{ route('admin.users.store') }}" class="space-y-4 px-6 py-6">
          @csrf
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
              <x-input-label for="name" value="{{ __('Name') }}" />
              <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ old('name') }}" required autofocus />
              <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>
            <div class="sm:col-span-2">
              <x-input-label for="email" value="{{ __('Email') }}" />
              <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" value="{{ old('email') }}" required />
              <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
            <div>
              <x-input-label for="password" value="{{ __('Password') }}" />
              <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required autocomplete="new-password" />
              <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>
            <div>
              <x-input-label for="password_confirmation" value="{{ __('Confirm') }}" />
              <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required autocomplete="new-password" />
            </div>
            <div class="sm:col-span-2">
              <x-input-label for="role" value="{{ __('Role') }}" />
              <select id="role" name="role" class="mt-1 block w-full rounded-lg border-zinc-300 text-sm shadow-sm focus:border-amber-500 focus:ring-amber-500">
                <option value="user" @selected(old('role') === 'user')>{{ __('Customer (shopper)') }}</option>
                <option value="admin" @selected(old('role') === 'admin')>{{ __('Admin (full access)') }}</option>
              </select>
              <x-input-error :messages="$errors->get('role')" class="mt-2" />
            </div>
          </div>
          <div class="flex flex-wrap items-center justify-end gap-3 pt-2">
            <button type="button" class="admin-btn" @click="inviteOpen = false">{{ __('Cancel') }}</button>
            <x-primary-button class="admin-btn-primary !border-zinc-900 !bg-zinc-900 !text-white hover:!bg-zinc-800">{{ __('Create account') }}</x-primary-button>
          </div>
        </form>
      </div>
    </div>
  </div>
  @include('admin.partials.luxe-footer', ['footerClass' => 'mt-8'])
</x-app-layout>
