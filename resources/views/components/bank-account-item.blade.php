@props([
    'account' => null,
])

<div class="flex items-center justify-between p-3 rounded-xl hover:bg-gray-50 transition cursor-pointer border border-gray-100 shadow-sm hover:border-gray-200">
    <div class="flex items-center gap-3">
        <div class="w-14 h-14 bg-gray-100 rounded-xl flex items-center justify-center shadow-sm"
             style="background-color: {{ $account->institution->color ?? '#6B7280' }}">
            @if($account->institution?->image)
                <x-institution-logo :image="$account->institution->image" :alt="$account->institution->name" white/>
            @else
                <span class="font-bold text-white text-lg">{{ strtoupper(substr($account->name, 0, 1)) }}</span>
            @endif
        </div>
        <div>
            <p class="font-semibold text-gray-900 text-sm">{{ $account->name }}</p>
            <p class="text-xs text-gray-500">{{ $account->account_type->label() }}</p>
        </div>
    </div>
    <div class="text-right">
        <p class="text-gray-500 text-sm">Saldo atual</p>
        <p class="font-bold text-gray-900 text-sm">R$ @moneyBr($account->current_balance)</p>
    </div>
</div>
