<nav x-data="{ open: false }" class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50">
    <div class="mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16 gap-2">

            <!-- Logo -->
            <div class="flex items-center min-w-0 shrink">
                <a href="{{ route('dashboard.index') }}" class="flex items-center group min-w-0">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center mr-2 sm:mr-3 shrink-0">
                        <img src="{{ asset('images/logo-fintrack.png') }}" alt="FinTrack Logo" class="h-8 w-auto max-h-full object-contain drop-shadow-md">
                    </div>
                    <span class="text-lg sm:text-xl font-black text-primary tracking-tight truncate">Fin</span>
                    <span class="text-lg sm:text-xl font-normal text-primary tracking-tight truncate">Track</span>
                </a>
            </div>

            <!-- Navigation Links (desktop) -->
            <div class="hidden md:flex items-center space-x-2 shrink-0">
                <x-nav-link :href="route('dashboard.index')" :active="request()->routeIs('dashboard.*')">
                    {{ ('Dashboard') }}
                </x-nav-link>

                <x-nav-link :href="route('transactions.index')" :active="request()->routeIs('transactions.*')">
                    {{ ('Extrato') }}
                </x-nav-link>

                <x-nav-link :href="route('accounts.index')" :active="request()->routeIs('accounts.*')">
                    {{ ('Contas') }}
                </x-nav-link>

                <x-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">
                    {{ ('Categorias') }}
                </x-nav-link>
            </div>

            <!-- User Menu + mobile toggle -->
            <div class="flex items-center gap-1 sm:gap-3 md:pl-4 md:border-l md:border-gray-200 shrink-0">
                <div class="text-right hidden sm:block max-w-[12rem] lg:max-w-none">
                    <p class="text-sm font-semibold text-gray-800 leading-tight truncate">
                        {{ Auth::user()->name }}
                    </p>
                    <p class="text-xs text-gray-500 leading-tight truncate">
                        {{ Auth::user()->email }}
                    </p>
                </div>

                <!-- Hamburger -->
                <button
                    type="button"
                    class="md:hidden inline-flex items-center justify-center p-2.5 min-h-[44px] min-w-[44px] rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500/40 transition"
                    x-on:click="open = ! open"
                    aria-expanded="false"
                    x-bind:aria-expanded="open"
                >
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <path x-show="!open" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path x-show="open" x-cloak class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button type="button" class="focus:outline-none focus:ring-2 focus:ring-blue-500/40 rounded-full min-h-[44px] min-w-[44px] flex items-center justify-center">
                            <img class="h-10 w-10 rounded-full border-2 border-white shadow-md"
                                 src="{{ Auth::user()->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=3b82f6&color=fff' }}"
                                 alt="{{ Auth::user()->name }}">
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Perfil') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                             onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Sair') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div
        class="md:hidden border-t border-gray-100 bg-white overflow-hidden transition-all duration-200 ease-out"
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-1"
    >
        <div class="pt-2 pb-4 space-y-0.5 px-2">
            <x-responsive-nav-link :href="route('dashboard.index')" :active="request()->routeIs('dashboard.*')" x-on:click="open = false">
                {{ ('Dashboard') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('transactions.index')" :active="request()->routeIs('transactions.*')" x-on:click="open = false">
                {{ ('Extrato') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('accounts.index')" :active="request()->routeIs('accounts.*')" x-on:click="open = false">
                {{ ('Contas') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')" x-on:click="open = false">
                {{ ('Categorias') }}
            </x-responsive-nav-link>
        </div>
    </div>
</nav>
