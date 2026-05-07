<x-guest-layout>
    <div class="bg-white rounded-xl shadow-2xl p-5 sm:p-8">

        <div class="text-start mb-6">
            <h2 class="text-xl font-bold text-gray-800">Verifique seu e-mail</h2>
            <p class="text-gray-500 mt-1 text-sm leading-relaxed">
                Obrigado por se cadastrar! Antes de começar, verifique seu e-mail clicando no link que enviamos para você.
            </p>
        </div>

        @if (session('status') === 'verification-link-sent')
            <div class="mb-6 p-3 rounded-xl bg-emerald-50 border border-emerald-200">
                <p class="text-sm font-medium text-emerald-700">
                    Um novo link de verificação foi enviado para o e-mail informado no cadastro.
                </p>
            </div>
        @endif

        <div class="flex flex-col gap-3">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf

                <button
                    type="submit"
                    class="w-full bg-primary hover:bg-primary-dark text-white font-semibold py-3 px-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5"
                >
                    Reenviar E-mail de Verificação
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button
                    type="submit"
                    class="w-full text-sm font-medium text-gray-500 hover:text-primary transition py-2"
                >
                    Sair da conta
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
