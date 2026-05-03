<x-guest-layout>
    <div class="bg-white rounded-xl shadow-2xl p-8">

        <div class="text-start mb-6">
            <h2 class="text-xl font-bold text-gray-800">Confirmar Senha</h2>
            <p class="text-gray-500 mt-1 text-sm leading-relaxed">
                Esta é uma área segura. Por favor, confirme sua senha antes de continuar.
            </p>
        </div>

        <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
            @csrf

            <x-form.input
                name="password"
                label="Senha"
                type="password"
                placeholder="••••••••"
                required
                autocomplete="current-password"
            />

            <button
                type="submit"
                class="w-full bg-primary hover:bg-primary-dark text-white font-semibold py-3 px-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5"
            >
                Confirmar
            </button>
        </form>
    </div>
</x-guest-layout>
