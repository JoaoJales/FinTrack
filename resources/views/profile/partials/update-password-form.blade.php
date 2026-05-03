<form method="post" action="{{ route('password.update') }}" class="space-y-5">
    @csrf
    @method('put')

    <div>
        <x-form.input
            id="update_password_current_password"
            name="current_password"
            label="Senha atual"
            type="password"
            autocomplete="current-password"
        />
        @error('current_password', 'updatePassword')
        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <x-form.input
            id="update_password_password"
            name="password"
            label="Nova senha"
            type="password"
            autocomplete="new-password"
        />
        @error('password', 'updatePassword')
        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <x-form.input
            id="update_password_password_confirmation"
            name="password_confirmation"
            label="Confirmar nova senha"
            type="password"
            autocomplete="new-password"
        />
        @error('password_confirmation', 'updatePassword')
        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex items-center gap-4 pt-2">
        <x-button-primary type="submit" class="py-2 px-5 gap-2">
            <i class="bx bx-lock-alt text-xl"></i>
            <span>Atualizar senha</span>
        </x-button-primary>

        @if (session('status') === 'password-updated')
            <p
                x-data="{ show: true }"
                x-show="show"
                x-transition
                x-init="setTimeout(() => show = false, 2000)"
                class="text-sm text-emerald-600 font-medium flex items-center gap-1"
            >
                <i class="bx bx-check-circle text-base"></i> Senha atualizada.
            </p>
        @endif
    </div>
</form>
