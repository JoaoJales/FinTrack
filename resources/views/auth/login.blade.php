<x-guest-layout>
    <!-- Card de Login -->
    <div class="bg-white rounded-xl shadow-2xl p-8">
        <!-- Status de Sessão (Ex: "Senha redefinida com sucesso") -->
        <x-breeze.auth-session-status class="mb-4" :status="session('status')" />

        <div class="text-start mb-4">
            <h2 class="text-xl font-bold text-gray-800">Acesse sua Conta</h2>
            <p class="text-gray-500 mt-1">Insira suas credenciais para continuar</p>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- E-mail -->
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">E-mail</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition"
                    placeholder="seu@email.com"
                    required
                    autofocus
                    autocomplete="username"
                >
                <x-breeze.input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-sm" />
            </div>

            <!-- Senha -->
            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Senha</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition"
                    placeholder="••••••••"
                    required
                    autocomplete="current-password"
                >
                <x-breeze.input-error :messages="$errors->get('password')" class="mt-2 text-red-500 text-sm" />
            </div>

            <!-- Lembrar-me & Esqueci a Senha -->
            <div class="flex items-center justify-between mb-6">
                <label for="remember_me" class="inline-flex items-center cursor-pointer">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-primary shadow-sm focus:ring-primary w-5 h-5 cursor-pointer" name="remember">
                    <span class="ms-2 text-sm text-gray-600">Lembrar de mim</span>
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm font-medium text-primary hover:text-primary-dark transition">
                        Esqueceu a senha?
                    </a>
                @endif
            </div>

            <!-- Botão Entrar -->
            <button
                type="submit"
                class="w-full bg-primary hover:bg-primary-dark text-white font-semibold py-3 px-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5"
            >
                Entrar
            </button>
        </form>

        <!-- Link para Cadastro -->
        <div class="mt-2 border-t border-gray-100 text-center pt-6">
            <p class="text-sm text-gray-600">
                Não tem uma conta?
                <a href="{{ route('register') }}" class="text-primary font-bold hover:underline decoration-2 underline-offset-4 transition-all">
                    Cadastre-se!
                </a>
            </p>
        </div>
    </div>
</x-guest-layout>
