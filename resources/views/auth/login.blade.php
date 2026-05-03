<x-guest-layout>
    <div class="bg-white rounded-xl shadow-2xl p-8">

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <div class="text-start mb-6">
            <h2 class="text-xl font-bold text-gray-800">Acesse sua Conta</h2>
            <p class="text-gray-500 mt-1">Insira suas credenciais para continuar</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <x-form.input
                name="email"
                label="E-mail"
                type="email"
                :value="old('email')"
                placeholder="seu@email.com"
                required
                autofocus
                autocomplete="username"
            />

            <x-form.input
                name="password"
                label="Senha"
                type="password"
                placeholder="••••••••"
                required
                autocomplete="current-password"
            />

            <div class="flex items-center justify-between">
                <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer">
                    <input
                        id="remember_me"
                        type="checkbox"
                        name="remember"
                        class="rounded border-gray-300 text-primary shadow-sm focus:ring-primary w-4 h-4 cursor-pointer"
                    >
                    <span class="text-sm text-gray-600">Lembrar de mim</span>
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm font-medium text-primary hover:text-primary-dark transition">
                        Esqueceu a senha?
                    </a>
                @endif
            </div>

            <button
                type="submit"
                class="w-full bg-primary hover:bg-primary-dark text-white font-semibold py-3 px-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5"
            >
                Entrar
            </button>
        </form>

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
