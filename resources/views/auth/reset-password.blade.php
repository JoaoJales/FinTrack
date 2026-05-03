<x-guest-layout>
    <div class="bg-white rounded-xl shadow-2xl p-8">

        <div class="text-start mb-6">
            <h2 class="text-xl font-bold text-gray-800">Nova Senha</h2>
            <p class="text-gray-500 mt-1">Defina uma nova senha para sua conta.</p>
        </div>

        <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <x-form.input
                name="email"
                label="E-mail"
                type="email"
                :value="old('email', $request->email)"
                required
                autofocus
                autocomplete="username"
            />

            <x-form.input
                name="password"
                label="Nova Senha"
                type="password"
                placeholder="Mínimo 8 caracteres"
                required
                autocomplete="new-password"
            />

            <x-form.input
                name="password_confirmation"
                label="Confirmar Nova Senha"
                type="password"
                placeholder="Digite a senha novamente"
                required
                autocomplete="new-password"
            />

            <button
                type="submit"
                class="w-full bg-primary hover:bg-primary-dark text-white font-semibold py-3 px-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5"
            >
                Redefinir Senha
            </button>
        </form>
    </div>
</x-guest-layout>
