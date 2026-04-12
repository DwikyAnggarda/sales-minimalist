<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="cupcake">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Sales Dashboard') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <!-- Theme Init (runs before paint to prevent flash) -->
    <script>
        (function() {
            var saved = localStorage.getItem('theme');
            var isDark = saved === 'forest' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches);
            document.documentElement.setAttribute('data-theme', isDark ? 'forest' : 'cupcake');
            document.documentElement.classList.toggle('dark', isDark);
        })();
    </script>
</head>

<body class="font-sans antialiased text-base-content bg-base-200/50 min-h-screen transition-colors duration-300">
    <div class="drawer lg:drawer-open">
        <input id="main-drawer" type="checkbox" class="drawer-toggle" />
        <div class="drawer-content flex flex-col pt-0">
            <!-- Navbar -->
            <div
                class="navbar bg-base-100/90 backdrop-blur border-b border-base-200/60 sticky top-0 z-30 shadow-[0_1px_2px_rgba(0,0,0,0.03)] px-4 sm:px-6 lg:px-8 h-16 transition-colors duration-300">
                <div class="flex-none lg:hidden">
                    <label for="main-drawer" aria-label="open sidebar" class="btn btn-square btn-ghost">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            class="inline-block w-5 h-5 stroke-current">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </label>
                </div>
                <div class="flex-1">
                    <a class="btn btn-ghost normal-case text-lg font-bold block lg:hidden">
                        {{ config('app.name', 'SalesDashboard') }}
                    </a>
                </div>
                <div class="flex-none gap-2 sm:gap-4">
                    <!-- Theme Toggle -->
                    <label
                        class="swap swap-rotate btn btn-ghost btn-circle btn-sm sm:btn-md text-base-content/70 hover:text-base-content">
                        <input type="checkbox" id="theme-toggle" />

                        <!-- sun icon (shown when NOT checked = light mode) -->
                        <svg class="swap-off fill-current w-5 h-5" xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24">
                            <path
                                d="M5.64,17l-.71.71a1,1,0,0,0,0,1.41,1,1,0,0,0,1.41,0l.71-.71A1,1,0,0,0,5.64,17ZM5,12a1,1,0,0,0-1-1H3a1,1,0,0,0,0,2H4A1,1,0,0,0,5,12Zm7-7a1,1,0,0,0,1-1V3a1,1,0,0,0-2,0V4A1,1,0,0,0,12,5ZM5.64,7.05a1,1,0,0,0,.7.29,1,1,0,0,0,.71-.29,1,1,0,0,0,0-1.41l-.71-.71A1,1,0,0,0,4.93,6.34Zm12,.29a1,1,0,0,0,.7-.29l.71-.71a1,1,0,1,0-1.41-1.41L17,5.64a1,1,0,0,0,0,1.41A1,1,0,0,0,17.66,7.34ZM21,11H20a1,1,0,0,0,0,2h1a1,1,0,0,0,0-2Zm-9,8a1,1,0,0,0-1,1v1a1,1,0,0,0,2,0V20A1,1,0,0,0,12,19ZM18.36,17A1,1,0,0,0,17,18.36l.71.71a1,1,0,0,0,1.41,0,1,1,0,0,0,0-1.41ZM12,6.5A5.5,5.5,0,1,0,17.5,12,5.51,5.51,0,0,0,12,6.5Zm0,9A3.5,3.5,0,1,1,15.5,12,3.5,3.5,0,0,1,12,15.5Z" />
                        </svg>

                        <!-- moon icon (shown when checked = dark mode) -->
                        <svg class="swap-on fill-current w-5 h-5" xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24">
                            <path
                                d="M21.64,13a1,1,0,0,0-1.05-.14,8.05,8.05,0,0,1-3.37.73A8.15,8.15,0,0,1,9.08,5.49a8.59,8.59,0,0,1,.25-2A1,1,0,0,0,8,2.36,10.14,10.14,0,1,0,22,14.05,1,1,0,0,0,21.64,13Zm-9.5,6.69A8.14,8.14,0,0,1,7.08,5.22v.27A10.15,10.15,0,0,0,17.22,15.63a9.79,9.79,0,0,0,2.1-.22A8.11,8.11,0,0,1,12.14,19.73Z" />
                        </svg>
                    </label>

                    <!-- User Menu -->
                    <div class="dropdown dropdown-end">
                        <label tabindex="0"
                            class="btn btn-ghost btn-circle avatar border-2 border-base-200/50 hover:border-primary/50 transition-colors">
                            <div class="w-9 h-9 rounded-full bg-base-300">
                                @if (auth()->check())
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=2563eb&color=fff&rounded=true&bold=true"
                                        alt="Avatar" />
                                @else
                                    <img src="https://ui-avatars.com/api/?name=Guest&background=94a3b8&color=fff&rounded=true&bold=true"
                                        alt="Avatar" />
                                @endif
                            </div>
                        </label>
                        <ul tabindex="0"
                            class="mt-4 z-[1] p-2 shadow-xl shadow-base-300/10 menu menu-sm dropdown-content bg-base-100 rounded-xl w-56 border border-base-200">
                            @auth
                                <li class="menu-title px-4 py-3">
                                    <div class="flex flex-col gap-0.5">
                                        <span class="font-bold text-base-content text-sm">{{ auth()->user()->name }}</span>
                                        <span
                                            class="text-xs text-base-content/60 truncate">{{ auth()->user()->email }}</span>
                                    </div>
                                </li>
                                <div class="divider my-0"></div>
                                <li>
                                    <a href="{{ route('profile.edit') ?? '#' }}"
                                        class="py-2 px-4 rounded-lg hover:bg-base-200" wire:navigate>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 opacity-70" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                            </path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        Settings
                                    </a>
                                </li>
                                <div class="divider my-0"></div>
                                <li>
                                    <form method="POST" action="{{ route('logout') ?? '#' }}" class="w-full m-0 p-0 block">
                                        @csrf
                                        <button type="submit"
                                            class="w-full text-left py-2 px-4 rounded-lg hover:bg-error/10 text-error flex items-center transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                                </path>
                                            </svg>
                                            Log out
                                        </button>
                                    </form>
                                </li>
                            @endauth
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <main class="flex-1 w-full p-4 sm:p-6 lg:p-8 max-w-[1600px] mx-auto">
                {{ $slot }}
            </main>
        </div>

        <!-- Sidebar Setup -->
        <div class="drawer-side z-40">
            <label for="main-drawer" aria-label="close sidebar" class="drawer-overlay"></label>
            <div
                class="w-72 h-full bg-base-100 border-r border-base-200 text-base-content shadow-[1px_0_2px_rgba(0,0,0,0.03)] relative flex flex-col transition-colors duration-300">
                <!-- Branding -->
                <div class="flex items-center gap-3 p-6 border-b border-base-200/60">
                    <div class="bg-primary text-primary-content p-2.5 rounded-xl shadow-sm shadow-primary/20">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <span
                        class="text-xl font-extrabold tracking-tight text-base-content whitespace-nowrap">Dashboard</span>
                </div>

                <div class="flex-1 overflow-y-auto w-full p-4">
                    <ul class="menu w-full px-0 gap-1">
                        <li class="menu-title text-xs font-bold opacity-50 tracking-widest uppercase mb-2">Main Menu
                        </li>
                        <li>
                            <a href="{{ route('dashboard') }}"
                                class="{{ request()->routeIs('dashboard') ? 'active bg-primary/10 text-primary font-medium' : 'hover:bg-base-200/50 text-base-content/80' }} rounded-xl py-3"
                                wire:navigate>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 opacity-80" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                </svg>
                                Analytics
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('daily-sales') }}"
                                class="{{ request()->routeIs('daily-sales') ? 'active bg-primary/10 text-primary font-medium' : 'hover:bg-base-200/50 text-base-content/80' }} rounded-xl py-3"
                                wire:navigate>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 opacity-80" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Daily Sales
                            </a>
                        </li>

                        <li class="menu-title text-xs font-bold opacity-50 tracking-widest uppercase mb-2 mt-6">Master Data
                        </li>
                        <li>
                            <a href="{{ route('products') }}"
                                class="{{ request()->routeIs('products') ? 'active bg-primary/10 text-primary font-medium' : 'hover:bg-base-200/50 text-base-content/80' }} rounded-xl py-3"
                                wire:navigate>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 opacity-80" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                Products
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Toast Container (fixed position, outside main content flow) --}}
    <div id="toast-container" class="toast toast-top toast-end z-[9999] fixed top-4 right-4"></div>

    @livewireScripts
    <script>
        // ── Theme Toggle ────────────────────────────────────────────────────
        function syncToggle() {
            var toggle = document.getElementById('theme-toggle');
            if (toggle) toggle.checked = (document.documentElement.getAttribute('data-theme') === 'forest');
        }
        function handleThemeChange(e) {
            var theme = e.target.checked ? 'forest' : 'cupcake';
            document.documentElement.setAttribute('data-theme', theme);
            document.documentElement.classList.toggle('dark', e.target.checked);
            localStorage.setItem('theme', theme);
        }
        function initThemeToggle() {
            var toggle = document.getElementById('theme-toggle');
            if (!toggle) return;
            syncToggle();
            toggle.removeEventListener('change', handleThemeChange);
            toggle.addEventListener('change', handleThemeChange);
        }
        // Re-apply saved theme on SPA navigation (fixes dark mode reset)
        function reapplyTheme() {
            var saved = localStorage.getItem('theme');
            var isDark = saved === 'forest' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches);
            document.documentElement.setAttribute('data-theme', isDark ? 'forest' : 'cupcake');
            document.documentElement.classList.toggle('dark', isDark);
            syncToggle();
        }
        document.addEventListener('DOMContentLoaded', function() { reapplyTheme(); initThemeToggle(); });
        document.addEventListener('livewire:navigated', function() { reapplyTheme(); initThemeToggle(); });

        // ── Toast System (single listener, no duplicates) ───────────────────
        (function() {
            function showToast(message, type) {
                var container = document.getElementById('toast-container');
                if (!container) return;

                // Clear any existing toasts first (only 1 toast at a time)
                container.innerHTML = '';

                var bgClass = type === 'success' ? 'bg-success text-success-content' : 'bg-error text-error-content';
                var icon = type === 'success' ? 'check_circle' : 'error';

                var el = document.createElement('div');
                el.className = 'alert ' + bgClass + ' shadow-lg rounded-xl text-sm font-bold flex items-center gap-2 min-w-[280px] border-none';
                el.innerHTML = '<span class="material-symbols-outlined text-lg">' + icon + '</span><span>' + message + '</span>';
                container.appendChild(el);

                setTimeout(function() {
                    el.style.opacity = '0';
                    el.style.transition = 'opacity 0.3s ease';
                    setTimeout(function() { el.remove(); }, 300);
                }, 3000);
            }

            // Register ONCE — this IIFE runs only once even with wire:navigate
            window.addEventListener('toast', function(e) {
                var d = e.detail;
                var data = Array.isArray(d) ? d[0] : d;
                showToast(data.message || 'Done', data.type || 'success');
            });
        })();
    </script>
</body>

</html>