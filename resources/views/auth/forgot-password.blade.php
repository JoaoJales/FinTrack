<x-guest-layout>
    <!-- Card de Recuperação de Senha -->
    <div class="bg-white rounded-xl shadow-2xl p-8">

        <div class="text-start mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Recuperar Senha</h2>
            <p class="text-gray-500 mt-2 text-sm line-height-relaxed">
                Esqueceu sua senha? Sem problemas. Informe seu e-mail abaixo e enviaremos um link para você escolher uma nova.
            </p>
        </div>

        <!-- Status da Sessão (Mensagem de sucesso ao enviar o e-mail) -->
        <x-auth-session-status class="mb-4 text-success font-medium" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <!-- Endereço de E-mail -->
            <div class="mb-6">
                <label for="email" class="block text-lg font-medium text-gray-700 mb-2">E-mail cadastrado</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition"
                    placeholder="seu@email.com"
                    required
                    autofocus
                >
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-sm" />
            </div>

            <div class="flex flex-col space-y-4">
                <!-- Botão de Envio -->
                <button
                    type="submit"
                    class="w-full bg-primary hover:bg-primary-dark text-white font-semibold py-3 px-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5"
                >
                    Enviar Link de Recuperação
                </button>

                <!-- Voltar para o Login -->
                <a href="{{ route('login') }}" class="text-center text-sm font-medium text-gray-500 hover:text-primary transition">
                    Voltar para o Login
                </a>
            </div>
        </form>
    </div>
</x-guest-layout>
