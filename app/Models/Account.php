<?php

namespace App\Models;

use App\Enums\AccountType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Account
 *
 * @property int $user_id Usuário
 * @property int $institution_id Instituição Financeira
 * @property string $name Apelido para Conta
 * @property float $initial_balance Saldo Inicial
 * @property AccountType $account_type Tipo de Conta (Corretente, Carteira, Poupança, Investimento, etc)
 * @property float $current_balance Saldo Atual (virtual — calculado via transações)
 */
class Account extends Model
{
    public $table = 'accounts';

    protected $fillable = [
        'user_id',
        'institution_id',
        'name',
        'initial_balance',
        'account_type',
        'is_default',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'institution_id' => 'integer',
        'name' => 'string',
        'initial_balance' => 'decimal:2',
        'account_type' => AccountType::class,
        'is_default' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function scopeDefault(Builder $query): Builder
    {
        return $query->where('is_default', true);
    }

    /**
     * Saldo atual = saldo inicial + entradas - saídas.
     * Atributo virtual — não existe no banco, calculado em tempo real.
     */
    protected function currentBalance(): Attribute
    {
        // O selectRaw() permite escrever SQL puro dentro da query do Eloquent.
        return Attribute::make(
            get: function () {
                $transactions = $this->transactions()
                    ->join('categories', 'transactions.category_id', '=', 'categories.id')
                    ->selectRaw("
                        SUM(CASE WHEN categories.type = 'income'  THEN transactions.amount ELSE 0 END) as total_income,
                        SUM(CASE WHEN categories.type = 'expense' THEN transactions.amount ELSE 0 END) as total_expense
                    ")
                    ->first();

                return (float) $this->initial_balance
                    + (float) ($transactions->total_income ?? 0)
                    - (float) ($transactions->total_expense ?? 0);
            }
        );
    }
}
