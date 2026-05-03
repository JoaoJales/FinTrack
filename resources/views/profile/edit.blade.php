<x-app-layout>
    <div class="max-w-2xl mx-auto space-y-6">

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Perfil</h1>
            <p class="text-gray-500 mt-1">Gerencie suas informações pessoais e segurança</p>
        </div>

        <!-- Informações do perfil -->
        <x-card>
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-800">Informações pessoais</h2>
                <p class="text-sm text-gray-400 mt-0.5">Atualize seu nome e endereço de e-mail.</p>
            </div>
            @include('profile.partials.update-profile-information-form')
        </x-card>

        <!-- Atualizar senha -->
        <x-card>
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-800">Senha</h2>
                <p class="text-sm text-gray-400 mt-0.5">Use uma senha longa e aleatória para manter sua conta segura.</p>
            </div>
            @include('profile.partials.update-password-form')
        </x-card>

        <!-- Excluir conta -->
        <x-card class="border border-red-100">
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-red-600">Excluir conta</h2>
                <p class="text-sm text-gray-400 mt-0.5">
                    Uma vez excluída, todos os dados serão permanentemente removidos.
                </p>
            </div>
            @include('profile.partials.delete-user-form')
        </x-card>

    </div>
</x-app-layout>
