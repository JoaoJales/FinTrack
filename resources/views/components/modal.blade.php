@props([
    'name', 'title' => '', 'width' => '',
    ])

<div
    x-data="{ open: false }"
    x-on:open-modal.window="$event.detail === '{{ $name }}' && (open = true)"
    x-on:close-modal.window="$event.detail === '{{ $name }}' || $event.detail === '' ? (open = false) : null"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center"
>
    {{-- Backdrop --}}
    <div
        class="absolute inset-0 bg-black/50" x-on:click="open = false" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
    ></div>

    {{-- Conteúdo --}}
    <div
        x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
        class="relative bg-white rounded-2xl shadow-xl w-full mx-4 z-10 {{ $width }}"
        x-on:click.stop
    >
        {{-- Header --}}
        @if($title || isset($headerTitle))
            <div class="flex items-center justify-between px-6 py-4 bg-blue-600 rounded-t-2xl">
                <h3 class="text-lg font-bold text-white">
                    @isset($headerTitle)
                        {{ $headerTitle }}
                    @else
                        {{ $title }}
                    @endisset
                </h3>
                <button class="text-white hover:text-gray-200 transition" x-on:click="open = false">
                    <i class="bx bx-x text-2xl"></i>
                </button>
            </div>
        @endif

        {{-- Body --}}
        <div class="px-6 py-5 max-h-[600px] overflow-y-auto">
            {{ $slot }}
        </div>

        {{-- Footer --}}
        @isset($footer)
            <footer class="flex justify-between gap-3 border-t border-gray-100 px-6 py-3">
                {{ $footer }}
            </footer>
        @endisset
    </div>
</div>

{{--@props([--}}
{{--    'title' => '',--}}
{{--    'content' => '',--}}
{{--    'footer' => '',--}}
{{--    'height' => 'lg:w-2/3',--}}
{{--    'name' => 'on',--}}
{{--])--}}

{{--<div>--}}

{{--    <div class="fixed z-30 inset-x-0 sm:inset-0 sm:flex sm:items-center sm:justify-center top-14 p-4 md:p-0"--}}
{{--         x-show="{{ $name }}" x-cloak>--}}

{{--        <div class="fixed inset-0 transition-opacity" x-show="{{ $name }}" x-cloak--}}
{{--             x-on:close-modal.window="{{ $name }} = false" x-on:keydown.escape.window="{{ $name }} = false"--}}
{{--             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"--}}
{{--             x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"--}}
{{--             x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">--}}
{{--            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>--}}
{{--        </div>--}}

{{--        <div class="bg-white dark:bg-gray-500 dark:text-gray-200 rounded-lg shadow-xl transform transition-all xs:w-full sm:w-full {{ $height }}"--}}
{{--             role="dialog" aria-modal="true" aria-labelledby="modal-headline" x-show="{{ $name }}"--}}
{{--             x-transition:enter="ease-out duration-300"--}}
{{--             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"--}}
{{--             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"--}}
{{--             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"--}}
{{--             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">--}}

{{--            <div class="flex flex-col">--}}
{{--                <header--}}
{{--                    class="flex justify-between text-center mb-2 border-b dark:border-slate-600 bg-emerald-700 px-4 py-3 rounded-t-lg">--}}
{{--                    <h4 class="w-full text-white flex items-center justify-between">--}}
{{--                        {{ $title }}--}}
{{--                        <button wire:loading--}}
{{--                                class="px-2 py-0 mr-2 rounded-md shadow-md left-36 border border-yellow-200 bg-yellow-100 text-slate-800 flex justify-between">--}}
{{--                            <i class="fas fa-circle-notch animate-spin mr-2"></i>--}}
{{--                            Carregando...--}}
{{--                        </button>--}}
{{--                    </h4>--}}
{{--                    <button class="text-white" @click="{{ $name }} = false"><i class="fa fa-times"></i></button>--}}
{{--                </header>--}}

{{--                <main class="px-4 max-h-[600px] overflow-y-auto">--}}
{{--                    {{ $content }}--}}
{{--                </main>--}}

{{--                <footer class="flex justify-between border-t dark:border-gray-600 px-4 py-2 mt-3">--}}
{{--                    {{ $footer }}--}}
{{--                </footer>--}}

{{--            </div>--}}
{{--        </div>--}}

{{--    </div>--}}

{{--</div>--}}
