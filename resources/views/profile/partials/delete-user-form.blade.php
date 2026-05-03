<div x-data="{}">
    <button
        type="button"
        class="inline-flex items-center gap-2 px-5 py-2 rounded-xl text-sm font-semibold text-white bg-red-500 hover:bg-red-600 transition-colors"
        x-on:click="$dispatch('confirm-dialog', {
            title: 'Excluir conta',
            message: 'Tem certeza que deseja excluir sua conta? Todos os seus dados serão permanentemente removidos. Esta ação não pode ser desfeita.',
            confirmLabel: 'Sim, excluir minha conta',
            confirmType: 'danger',
            onConfirm: () => $refs.formDeleteAccount.submit()
        })"
    >
        <i class="bx bx-trash text-lg"></i>
        Excluir minha conta
    </button>

    <form
        x-ref="formDeleteAccount"
        method="post"
        action="{{ route('profile.destroy') }}"
        class="hidden"
    >
        @csrf
        @method('delete')
    </form>
</div>
