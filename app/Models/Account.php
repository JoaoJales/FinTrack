<?php

namespace App\Models;

use App\Enums\AccountType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Account
 *
 * @property int $user_id Usuário
 * @property int $institution_id Instituição Financeira
 * @property string $name Apelido para Conta
 * @property double $initial_balance Saldo Inicial
 * @property AccountType $account_type Tipo de Conta (Corretente, Carteira, Poupança, Investimento, etc)
 */
class Account extends Model
{
    use SoftDeletes;

    public $table = 'accounts';
    protected $fillable = [
        'user_id',
        'institution_id',
        'name',
        'initial_balance',
        'account_type',
    ];
    protected $casts = [
        'user_id' => 'integer',
        'institution_id' => 'integer',
        'name' => 'string',
        'initial_balance' => 'decimal:2',
        'account_type' => AccountType::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function institution(): BelongsTo {
        return $this->belongsTo(Institution::class);
    }

    public function transactions(): HasMany {
        return $this->hasMany(Transaction::class);
    }
}
