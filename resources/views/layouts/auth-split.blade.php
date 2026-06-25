@php
    $brandName = ! empty(trim((string) ($site['site_display_name'] ?? ''))) ? trim((string) $site['site_display_name']) : config('app.name', 'Laravel');
    $logoPath = trim((string) ($site['logo_path'] ?? ''));
    if ($logoPath === '') {
        $logoPath = trim((string) ($site['logo_url'] ?? ''));
    }
    $hasLogo = $logoPath !== '';
    $panelPath = trim((string) ($site['auth_panel_image_path'] ?? ''));
    $authPanelFallback = public_path('asset/images/powerblink/placeholder.svg');
    $useAuthPanelFallback = $panelPath === '' && is_file($authPanelFallback);

    $authPatternRel = null;
    foreach ([
        'section-pattern.svg', 'section-pattern.png', 'section-pattern.webp', 'section-pattern.jpg',
        'section-patern.svg', 'section-patern.png', 'section-patern.webp', 'section-patern.jpg',
        'asset/section-pattern.svg', 'asset/section-pattern.png', 'asset/section-pattern.webp', 'asset/section-pattern.jpg',
    ] as $candidate) {
        if (is_file(public_path($candidate))) {
            $authPatternRel = $candidate;
            break;
        }
    }
    $authPatternUrl = $authPatternRel !== null ? asset($authPatternRel) : null;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full overflow-x-hidden">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $brandName }} — {{ __('Account') }}</title>
        <style>[x-cloak]{display:none!important}</style>
        {{-- Split layout in plain CSS so it never depends on Tailwind purge/build for two columns. --}}
        <style>
            .auth-split {
                display: flex;
                flex-direction: column;
                min-height: 100vh;
                width: 100%;
                max-width: 100vw;
                overflow-x: hidden;
                background: #f4f4f5;
            }
            .auth-split__panel {
                position: relative;
                isolation: isolate;
                flex-shrink: 0;
                width: 100%;
                min-height: 40vh;
                overflow: hidden;
            }
            .auth-split__form {
                position: relative;
                z-index: 2;
                flex: 1 1 auto;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                min-width: 0;
                padding: 2.5rem 1.5rem;
            }
            .auth-split__form-inner {
                width: 100%;
                max-width: 100%;
                display: flex;
                flex-direction: column;
                align-items: center;
            }
            .auth-split__brand {
                display: inline-flex;
                width: auto;
                justify-content: center;
                align-items: center;
            }
            @media (min-width: 768px) {
                .auth-split {
                    flex-direction: row;
                    height: 100vh;
                    min-height: 100vh;
                    overflow: hidden;
                }
                .auth-split__panel {
                    flex: 0 0 50%;
                    width: 50%;
                    max-width: 50%;
                    min-height: 0;
                    height: 100%;
                }
                .auth-split__form {
                    flex: 0 0 50%;
                    width: 50%;
                    max-width: 50%;
                    height: 100%;
                    overflow-x: hidden;
                    overflow-y: auto;
                    padding: 3.5rem 2rem;
                }
            }
            @media (min-width: 1024px) {
                .auth-split__form {
                    padding-left: 2.5rem;
                    padding-right: 2.5rem;
                }
            }
        </style>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @include('partials.vite-assets')
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="auth-split">
            <div class="auth-split__panel">
                @if ($panelPath !== '')
                    <img
                        src="{{ \App\Support\MediaImageUrl::url($panelPath) }}"
                        alt=""
                        class="absolute inset-0 z-0 h-full w-full object-cover"
                        decoding="async"
                    />
                @elseif ($useAuthPanelFallback)
                    <img
                        src="{{ asset('asset/images/powerblink/placeholder.svg') }}"
                        alt=""
                        class="absolute inset-0 z-0 h-full w-full object-cover opacity-95"
                    />
                @else
                    <div class="absolute inset-0 z-0 bg-gradient-to-br from-zinc-600 via-zinc-800 to-zinc-950" aria-hidden="true"></div>
                @endif

                {{-- Stack: photo (z-0) → dark veil (z-1) → pattern on top (z-2). Slightly lighter veil when a pattern file exists so PNG alpha doesn’t read as invisible. --}}
                <div class="pointer-events-none absolute inset-0 z-[1] {{ ! empty($authPatternUrl) ? 'bg-black/40' : 'bg-black/55' }}" aria-hidden="true"></div>

                @if (! empty($authPatternUrl))
                    <div
                        class="pointer-events-none absolute inset-0 z-[2] bg-repeat brightness-125 contrast-125 opacity-[0.95]"
                        style="background-image: url('{{ $authPatternUrl }}'); background-size: 220px 220px; background-position: 0 0;"
                        aria-hidden="true"
                    ></div>
                @endif
            </div>

            <div class="auth-split__form">
                <div class="auth-split__form-inner">
                    <a href="{{ url('/') }}" class="auth-split__brand">
                        @if ($hasLogo)
                            <img
                                src="{{ \App\Support\MediaImageUrl::url($logoPath) }}"
                                alt="{{ $brandName }}"
                                class="h-12 w-auto max-h-14 max-w-[min(100%,16rem)] object-contain sm:h-14 sm:max-h-16"
                            />
                        @else
                            <span class="text-center text-2xl font-bold tracking-tight text-zinc-900 sm:text-3xl">{{ $brandName }}</span>
                        @endif
                    </a>

                    <div class="mt-8 w-full overflow-hidden rounded-lg bg-white p-6 shadow-md sm:p-8">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
        @stack('body-end')
    </body>
</html>
