<button type="button"
    {{ $attributes->merge(['class' => 'inline-flex items-center text-sm py-1 px-2 rounded rounded-md border-2 border-slate-400 hover:bg-slate-100']) }}>
    {{ $slot }}
</button>
