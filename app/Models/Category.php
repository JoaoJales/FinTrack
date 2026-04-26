<?php

namespace App\Models;

use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Category
 *
 * @property string $name Nome da Categoria
 * @property int $user_id Usuário (Nullable)
 * @property string $icon Ícone opcional da Categoria
 * @property string $color Cor opcional da Categoria
 * @property TransactionType $type Tipo da transação (Ganho e Gasto)
 * @property bool $is_editable Diferencia se a categoria é global ou criada pelo usuário
 */
class Category extends Model
{
    use SoftDeletes;

    protected $table = 'categories';

    protected $fillable = [
        'name',
        'user_id',
        'icon',
        'color',
        'type',
        'is_editable',
    ];

    protected $casts = [
        'name' => 'string',
        'user_id' => 'integer',
        'icon' => 'string',
        'color' => 'string',
        'type' => TransactionType::class,
        'is_editable' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
