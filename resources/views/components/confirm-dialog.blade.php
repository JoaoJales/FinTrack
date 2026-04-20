<div
    x-data="{
        open: false,
        title: '',
        message: '',
        confirmLabel: 'Confirmar',
        cancelLabel: 'Cancelar',
        confirmType: 'danger',
        onConfirm: null,

        show({ title, message, confirmLabel = 'Confirmar', cancelLabel = 'Cancelar', confirmType = 'danger', onConfirm }) {
            this.title        = title;
            this.message      = message;
            this.confirmLabel = confirmLabel;
            this.cancelLabel  = cancelLabel;
            this.confirmType  = confirmType;
            this.onConfirm    = onConfirm;
            this.open         = true;
        },

        confirm() {
            if (this.onConfirm) this.onConfirm();
            this.open = false;
        }
    }"
    x-on:confirm-dialog.window="show($event.detail)"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-[998] flex items-center justify-center"
>
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/50" x-on:click="open = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"></div>

    {{-- Card --}}
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4 z-10 overflow-hidden"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100">

        {{-- Ícone + Título --}}
        <div class="p-6 flex flex-col items-center text-center gap-3">
            <div
                class="w-14 h-14 rounded-full flex items-center justify-center"
                :class="confirmType === 'danger' ? 'bg-red-100' : 'bg-blue-100'"
            >
                <i
                    class="text-3xl"
                    :class="confirmType === 'danger' ? 'bx bx-trash text-red-500' : 'bx bx-question-mark text-blue-500'"
                ></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900" x-text="title"></h3>
            <p class="text-sm text-gray-500" x-text="message"></p>
        </div>

        {{-- Botões --}}
        <div class="flex border-t border-gray-100">
            <button
                type="button"
                class="flex-1 py-3 text-sm font-medium text-gray-600 hover:bg-gray-50 transition"
                x-on:click="open = false"
                x-text="cancelLabel"
            ></button>
            <button
                type="button"
                class="flex-1 py-3 text-sm font-bold transition border-l border-gray-100"
                :class="confirmType === 'danger' ? 'text-red-500 hover:bg-red-50' : 'text-blue-500 hover:bg-blue-50'"
                x-on:click="confirm()"
                x-text="confirmLabel"
            ></button>
        </div>
    </div>
</div>
