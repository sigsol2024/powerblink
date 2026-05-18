<x-app-layout>
  <x-slot name="header">
    <div class="admin-page-header flex flex-col gap-2 sm:gap-3">
      <h2 class="admin-page-title">{{ __('Users & dealers') }}</h2>
      <div class="admin-header-actions flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-center">
        <button type="button" class="admin-btn-primary !border-zinc-900 !bg-zinc-900 !text-white hover:!bg-zinc-800" @click="$dispatch('open-invite-modal')">
          {{ __('Invite or create account') }}
        </button>
      </div>
    </div>
  </x-slot>

  <div
    class="w-full space-y-8"
    x-data="{ inviteOpen: {{ $errors->any() ? 'true' : 'false' }}, openId: null, toggleOpen(id) { this.openId = this.openId === id ? null : id; } }"
    @open-invite-modal.window="inviteOpen = true"
    @keydown.escape.window="inviteOpen = false"
  >
    <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm">
      <div class="border-b border-zinc-100 px-6 py-4">
        <h3 class="text-base font-bold text-zinc-900">{{ __('All accounts') }}</h3>
        <p class="mt-1 text-sm text-zinc-600">{{ __('Admins manage the site; dealers own their listings.') }}</p>
      </div>
      <div class="hidden lg:block overflow-x-auto p-4 sm:p-6">
        <table class="min-w-full divide-y divide-zinc-200 text-sm">
          <thead>
            <tr class="text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
              <th class="px-3 py-2">{{ __('Name') }}</th>
              <th class="px-3 py-2">{{ __('Email') }}</th>
              <th class="px-3 py-2">{{ __('Roles') }}</th>
              <th class="px-3 py-2 text-right">{{ __('Actions') }}</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-zinc-100">
            @foreach($users as $user)
              <tr>
                <td class="px-3 py-3 font-medium text-zinc-900">{{ $user->name }}</td>
                <td class="px-3 py-3 text-zinc-600">{{ $user->email }}</td>
                <td class="px-3 py-3">
                  <div class="flex flex-wrap gap-1">
                    @foreach($user->roles as $role)
                      @if($role->name === 'admin')
                        <span class="inline-flex rounded-full bg-violet-100 px-2.5 py-0.5 text-xs font-semibold text-violet-800">{{ __('Admin') }}</span>
                      @elseif($role->name === 'user')
                        <span class="inline-flex rounded-full bg-sky-100 px-2.5 py-0.5 text-xs font-semibold text-sky-800">{{ __('Dealer') }}</span>
                      @else
                        <span class="inline-flex rounded-full bg-zinc-100 px-2.5 py-0.5 text-xs text-zinc-700">{{ $role->name }}</span>
                      @endif
                    @endforeach
                  </div>
                </td>
                <td class="px-3 py-3 text-right">
                  @if($user->id === auth()->id())
                    <span class="text-xs text-zinc-400">{{ __('You') }}</span>
                  @else
                    <form method="post" action="{{ route('admin.users.destroy', $user) }}" class="inline" onsubmit="return confirm(@json(__('Delete this user?')));">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="text-sm font-medium text-rose-700 hover:underline">{{ __('Delete') }}</button>
                    </form>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="lg:hidden space-y-3 p-4 sm:p-6">
        @foreach($users as $user)
          <article class="overflow-hidden rounded-lg border border-zinc-200 bg-white">
            <button type="button" class="flex w-full items-center justify-between gap-3 px-4 py-3 text-left" @click="toggleOpen({{ $user->id }})" :aria-expanded="openId === {{ $user->id }} ? 'true' : 'false'">
              <span class="min-w-0 flex-1 truncate font-semibold text-zinc-900">{{ $user->name }}</span>
              <svg class="h-5 w-5 shrink-0 text-zinc-400 transition-transform" :class="openId === {{ $user->id }} ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="openId === {{ $user->id }}" x-cloak class="border-t border-zinc-100 bg-zinc-50 px-4 py-4 text-sm">
              <p class="text-zinc-600">{{ $user->email }}</p>
              <div class="mt-2 flex flex-wrap gap-1">
                @foreach($user->roles as $role)
                  @if($role->name === 'admin')
                    <span class="inline-flex rounded-full bg-violet-100 px-2.5 py-0.5 text-xs font-semibold text-violet-800">{{ __('Admin') }}</span>
                  @elseif($role->name === 'user')
                    <span class="inline-flex rounded-full bg-sky-100 px-2.5 py-0.5 text-xs font-semibold text-sky-800">{{ __('Dealer') }}</span>
                  @else
                    <span class="inline-flex rounded-full bg-zinc-100 px-2.5 py-0.5 text-xs text-zinc-700">{{ $role->name }}</span>
                  @endif
                @endforeach
              </div>
              <div class="mt-4 flex flex-col gap-2">
                @if($user->id === auth()->id())
                  <span class="text-xs text-zinc-400">{{ __('You') }}</span>
                @else
                  <form method="post" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm(@json(__('Delete this user?')));">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="admin-btn w-full justify-start text-rose-700">{{ __('Delete') }}</button>
                  </form>
                @endif
              </div>
            </div>
          </article>
        @endforeach
      </div>
      <div class="border-t border-zinc-100 px-6 py-4">
        {{ $users->links() }}
      </div>
    </div>

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
            <p class="mt-1 text-sm text-zinc-600">{{ __('Choose Admin for full console access, or Dealer for users who only list vehicles.') }}</p>
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
                <option value="user" @selected(old('role') === 'user')>{{ __('Dealer (list vehicles)') }}</option>
                <option value="admin" @selected(old('role') === 'admin')>{{ __('Admin (full access)') }}</option>
              </select>
              <x-input-error :messages="$errors->get('role')" class="mt-2" />
            </div>
          </div>
          <div class="flex flex-wrap items-center justify-end gap-3 pt-2">
            <button type="button" class="admin-btn" @click="inviteOpen = false">{{ __('Cancel') }}</button>
            <x-primary-button class="admin-btn-primary !border-zinc-900 !bg-zinc-900 !text-white hover:!bg-zinc-800">{{ __('Create user') }}</x-primary-button>
          </div>
        </form>
      </div>
    </div>
  </div>
</x-app-layout>
