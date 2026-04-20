<nav class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50">
    <div class="mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ route('dashboard.index') }}" class="flex items-center group">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center mr-3">
                        <img src="{{ asset('images/logo-fintrack.png') }}" alt="FinTrack Logo" class="w-32 h-auto object-contain drop-shadow-md">
                    </div>
                    <span class="text-xl font-black text-primary tracking-tight">Fin</span>
                    <span class="text-xl font-normal text-primary tracking-tight">Track</span>
                </a>
            </div>

            <!-- Navigation Links (desktop) -->
            <div class="hidden md:flex items-center space-x-2">
                <x-nav-link :href="route('dashboard.index')" :active="request()->routeIs('dashboard.*')">
                    {{ ('Dashboard') }}
                </x-nav-link>

                <x-nav-link :href="route('transactions.index')" :active="request()->routeIs('transactions.*')">
                    {{ ('Transações') }}
                </x-nav-link>

                <x-nav-link :href="route('accounts.index')" :active="request()->routeIs('accounts.*')">
                    {{ ('Minhas Contas') }}
                </x-nav-link>
            </div>

            <!-- User Menu (desktop) -->
            <div class="flex items-center space-x-3 pl-4 border-l border-gray-200">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                </div>
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="focus:outline-none">
                            <img class="h-10 w-10 rounded-full border-2 border-white shadow-md"
                                 src="{{ Auth::user()->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=3b82f6&color=fff' }}"
                                 alt="{{ Auth::user()->name }}">
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Perfil') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
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
</nav>
