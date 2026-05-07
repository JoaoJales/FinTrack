<x-guest-layout>
    <div class="bg-white rounded-xl shadow-2xl p-5 sm:p-8">

        <div class="text-start mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Recuperar Senha</h2>
            <p class="text-gray-500 mt-2 text-sm leading-relaxed">
                Esqueceu sua senha? Sem problemas. Informe seu e-mail abaixo e enviaremos um link para você escolher uma nova.
            </p>
        </div>

        @if (session('status'))
            <div class="mb-4 p-3 rounded-xl bg-emerald-50 border border-emerald-200">
                <p class="text-sm font-medium text-emerald-700">{{ session('status') }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf

            <x-form.input
                name="email"
                label="E-mail cadastrado"
                type="email"
                :value="old('email')"
                placeholder="seu@email.com"
                required
                autofocus
                autocomplete="username"
            />

            <button
                type="submit"
                class="w-full bg-primary hover:bg-primary-dark text-white font-semibold py-3 px-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5"
            >
                Enviar Link de Recuperação
            </button>

            <a href="{{ route('login') }}" class="block text-center text-sm font-medium text-gray-500 hover:text-primary transition">
                Voltar para o Login
            </a>
        </form>
    </div>
</x-guest-layout>
