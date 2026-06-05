<?php

namespace App\Models;

use App\Enums\TransactionType;
use Database\Factories\TransactionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Transaction
 *
 * @property int $user_id Usuário
 * @property int $account_id Conta Bancária (origem)
 * @property int|null $destination_account_id Conta destino (transferências)
 * @property int|null $category_id Categoria
 * @property TransactionType $type Tipo da transação
 * @property float $amount Valor da Transação
 * @property $date Data da Transação
 * @property string $description Descrição
 */
class Transaction extends Model
{
    /** @use HasFactory<TransactionFactory> */
    use HasFactory;

    protected $table = 'transactions';

    protected $fillable = [
        'user_id',
        'account_id',
        'destination_account_id',
        'category_id',
        'type',
        'amount',
        'date',
        'description',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'account_id' => 'integer',
        'destination_account_id' => 'integer',
        'category_id' => 'integer',
        'type' => TransactionType::class,
        'amount' => 'decimal:2',
        'date' => 'date',
        'description' => 'string',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function destinationAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'destination_account_id');
    }

    public function isTransfer(): bool
    {
        return $this->type === TransactionType::TRANSFER;
    }
}
