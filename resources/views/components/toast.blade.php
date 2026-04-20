<div
    x-data="{
        toasts: [],
        add(toast) {
            const id = Date.now();
            this.toasts.push({ id, ...toast });
            setTimeout(() => this.remove(id), toast.duration ?? 4000);
        },
        remove(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
        }
    }"
    x-on:toast.window="add($event.detail)"
    class="fixed top-16 left-1/2 -translate-x-1/2 z-[999] flex flex-col gap-3 items-center"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="true"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-4"
            class="flex items-center gap-3 px-4 py-3 rounded-xl shadow-lg text-white text-sm font-medium min-w-[280px]"
            :class="{
                'bg-emerald-500': toast.type === 'success',
                'bg-red-500':     toast.type === 'error',
                'bg-amber-500':   toast.type === 'warning',
                'bg-blue-500':    toast.type === 'info',
            }"
        >
            <i :class="{
                'bx bx-check-circle text-xl':       toast.type === 'success',
                'bx bx-x-circle text-xl':           toast.type === 'error',
                'bx bx-error text-xl':              toast.type === 'warning',
                'bx bx-info-circle text-xl':        toast.type === 'info',
            }"></i>
            <span x-text="toast.message" class="flex-1"></span>
            <button x-on:click="remove(toast.id)" class="ml-2 hover:opacity-75 transition">
                <i class="bx bx-x text-lg"></i>
            </button>
        </div>
    </template>
</div>
