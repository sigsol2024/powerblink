<x-app-layout>
    <x-admin.page-header :title="__('Welcome')" />
    <x-admin.page-content>
        <x-admin.card>
            <p class="text-on-surface-variant">{{ __('Signed in as :email', ['email' => Auth::user()->email]) }}</p>
            <p class="mt-4 text-sm text-on-surface-variant">{{ __('Use the portal menu to view your academy dashboard, or visit the public site.') }}</p>
            <div class="mt-6 flex flex-wrap gap-3">
                @if (Auth::user()->isMember())
                    <x-admin.button variant="primary" :href="route('portal.dashboard')">{{ __('Open portal') }}</x-admin.button>
                @endif
                <x-admin.button variant="primary" :href="route('home')" target="_blank" rel="noopener">{{ __('View site') }}</x-admin.button>
            </div>
        </x-admin.card>
    </x-admin.page-content>
</x-app-layout>
