<x-app-layout>
    <x-slot name="header">
        <div class="admin-page-header flex flex-col gap-2 sm:gap-3">
            <h2 class="admin-page-title">{{ __('Profile') }}</h2>
            <p class="text-xs font-medium uppercase tracking-wider text-zinc-500">{{ __('Account & security') }}</p>
        </div>
    </x-slot>

    <div class="mx-auto w-full max-w-3xl space-y-6 pb-10">
        <div class="rounded-2xl border border-zinc-200/90 bg-white p-6 shadow-sm sm:p-8">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="rounded-2xl border border-zinc-200/90 bg-white p-6 shadow-sm sm:p-8">
            @include('profile.partials.update-password-form')
        </div>

        <div class="rounded-2xl border border-zinc-200/90 bg-white p-6 shadow-sm sm:p-8">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-app-layout>
