<x-guest-layout>
    <!-- Card de Registro -->
    <div class="bg-white rounded-xl shadow-2xl p-8">
        <div class="text-start mb-4">
            <h2 class="text-xl font-bold text-gray-800">Crie sua conta</h2>
            <p class="text-gray-500 mt-1">Comece a organizar sua vida financeira hoje!</p>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <!-- Nome -->
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nome Completo</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition"
                    placeholder="Como deseja ser chamado?"
                    required
                    autofocus
                    autocomplete="name"
                >
                <x-breeze.input-error :messages="$errors->get('name')" class="mt-2 text-red-500 text-sm" />
            </div>

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
                    autocomplete="username"
                >
                <x-breeze.input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-sm" />
            </div>

            <!-- Senha -->
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Senha</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition"
                    placeholder="Mínimo 8 caracteres"
                    required
                    autocomplete="new-password"
                >
                <x-breeze.input-error :messages="$errors->get('password')" class="mt-2 text-red-500 text-sm" />
            </div>

            <!-- Confirmar Senha -->
            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirmar Senha</label>
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition"
                    placeholder="Digite a senha novamente"
                    required
                    autocomplete="new-password"
                >
                <x-breeze.input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-500 text-sm" />
            </div>

            <!-- Botão Cadastrar -->
            <button
                type="submit"
                class="w-full bg-primary hover:bg-primary-dark text-white font-semibold py-3 px-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5"
            >
                Criar Minha Conta
            </button>
        </form>

        <!-- Link para Voltar ao Login -->
        <div class="mt-2 border-t border-gray-100 text-center pt-6">
            <p class="text-sm text-gray-600">
                Já possui uma conta?
                <a href="{{ route('login') }}" class="text-primary font-bold hover:underline decoration-2 underline-offset-4 transition-all">
                    Fazer Login
                </a>
            </p>
        </div>
    </div>
</x-guest-layout>
