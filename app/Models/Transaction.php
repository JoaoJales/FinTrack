<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Transaction
 *
 * @property int $user_id Usuário
 * @property int $account_id Conta Bancária
 * @property int $category_id Categoria
 * @property double $amount Valor da Transação
 * @property $date Data da Transação
 * @property string $description Descrição
 */
class Transaction extends Model
{
    use softDeletes;

    protected $table = 'transactions';
    protected $fillable = [
        'user_id',
        'account_id',
        'category_id',
        'amount',
        'date',
        'description',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'account_id' => 'integer',
        'category_id' => 'integer',
        'amount' => 'decimal:2',
        'date' => 'date',
        'description' => 'string',
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo {
        return $this->belongsTo(Category::class);
    }

    public function account(): BelongsTo {
        return $this->belongsTo(Account::class);
    }


}
