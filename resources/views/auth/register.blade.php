<x-guest-layout>
    <div class="bg-white rounded-xl shadow-2xl p-8">

        <div class="text-start mb-6">
            <h2 class="text-xl font-bold text-gray-800">Crie sua conta</h2>
            <p class="text-gray-500 mt-1">Comece a organizar sua vida financeira hoje!</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <x-form.input
                name="name"
                label="Nome Completo"
                type="text"
                :value="old('name')"
                placeholder="Como deseja ser chamado?"
                required
                autofocus
                autocomplete="name"
            />

            <x-form.input
                name="email"
                label="E-mail"
                type="email"
                :value="old('email')"
                placeholder="seu@email.com"
                required
                autocomplete="username"
            />

            <x-form.input
                name="password"
                label="Senha"
                type="password"
                placeholder="Mínimo 8 caracteres"
                required
                autocomplete="new-password"
            />

            <x-form.input
                name="password_confirmation"
                label="Confirmar Senha"
                type="password"
                placeholder="Digite a senha novamente"
                required
                autocomplete="new-password"
            />

            <button
                type="submit"
                class="w-full bg-primary hover:bg-primary-dark text-white font-semibold py-3 px-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5"
            >
                Criar Minha Conta
            </button>
        </form>

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
