@props([
    'name', 'title' => '', 'width' => '',
    ])

<div
    x-data="{ open: false }"
    x-on:open-modal.window="$event.detail === '{{ $name }}' && (open = true)"
    x-on:close-modal.window="$event.detail === '{{ $name }}' || $event.detail === '' ? (open = false) : null"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 flex items-end justify-center sm:items-center p-0 sm:p-4"
>
    {{-- Backdrop --}}
    <div
        class="absolute inset-0 bg-black/50" x-on:click="open = false" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
    ></div>

    {{-- Conteúdo --}}
    <div
        x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 sm:scale-95 translate-y-4 sm:translate-y-0"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-4 sm:translate-y-0"
        class="relative bg-white rounded-t-2xl sm:rounded-2xl shadow-xl w-full z-10 max-h-[90vh] flex flex-col mx-0 sm:mx-auto {{ $width ?: 'max-w-lg sm:max-w-xl' }}"
        x-on:click.stop
        role="dialog"
        aria-modal="true"
    >
        {{-- Header --}}
        @if($title || isset($headerTitle))
            <div class="flex items-center justify-between px-4 sm:px-6 py-4 bg-blue-600 rounded-t-2xl shrink-0">
                <h3 class="text-lg font-bold text-white pr-2 truncate">
                    @isset($headerTitle)
                        {{ $headerTitle }}
                    @else
                        {{ $title }}
                    @endisset
                </h3>
                <button type="button" class="text-white hover:text-gray-200 transition shrink-0 min-h-[44px] min-w-[44px] flex items-center justify-center rounded-lg" x-on:click="open = false" aria-label="Fechar">
                    <i class="bx bx-x text-2xl"></i>
                </button>
            </div>
        @endif

        {{-- Body --}}
        <div class="px-4 sm:px-6 py-5 overflow-y-auto flex-1 min-h-0 max-h-[min(600px,calc(90vh-9rem))]">
            {{ $slot }}
        </div>

        {{-- Footer --}}
        @isset($footer)
            <footer class="flex flex-col gap-2 sm:flex-row sm:justify-between sm:items-center sm:gap-3 border-t border-gray-100 px-4 sm:px-6 py-3 shrink-0 pb-[max(0.75rem,env(safe-area-inset-bottom))]">
                {{ $footer }}
            </footer>
        @endisset
    </div>
</div>
