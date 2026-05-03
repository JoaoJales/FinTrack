<form id="send-verification" method="post" action="{{ route('verification.send') }}">
    @csrf
</form>

<form method="post" action="{{ route('profile.update') }}" class="space-y-5">
    @csrf
    @method('patch')

    <x-form.input
        id="name"
        name="name"
        label="Nome"
        type="text"
        :value="old('name', $user->name)"
        autofocus
        autocomplete="name"
    />

    <div>
        <x-form.input
            id="email"
            name="email"
            label="E-mail"
            type="email"
            :value="old('email', $user->email)"
            autocomplete="username"
        />

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="mt-2 p-3 rounded-xl bg-amber-50 border border-amber-200">
                <p class="text-sm text-amber-700">
                    Seu e-mail ainda não foi verificado.
                    <button form="send-verification"
                            class="underline font-medium hover:text-amber-900 transition-colors">
                        Clique aqui para reenviar o e-mail de verificação.
                    </button>
                </p>

                @if (session('status') === 'verification-link-sent')
                    <p class="mt-1 text-sm font-medium text-emerald-600">
                        Um novo link de verificação foi enviado para o seu e-mail.
                    </p>
                @endif
            </div>
        @endif
    </div>

    <div class="flex items-center gap-4 pt-2">
        <x-button-primary type="submit" class="py-2 px-5 gap-2">
            <i class="bx bx-check text-xl"></i>
            <span>Salvar alterações</span>
        </x-button-primary>

        @if (session('status') === 'profile-updated')
            <p
                x-data="{ show: true }"
                x-show="show"
                x-transition
                x-init="setTimeout(() => show = false, 2000)"
                class="text-sm text-emerald-600 font-medium flex items-center gap-1"
            >
                <i class="bx bx-check-circle text-base"></i> Salvo com sucesso.
            </p>
        @endif
    </div>
</form>
