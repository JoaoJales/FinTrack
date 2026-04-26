<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Institution
 *
 * @property string $name Nome da Instituição Financeira
 * @property string $image Caminho para imagem da Instituição
 * @property string $color Cor principal da Instituição em Hexadecimal
 */
class Institution extends Model
{
    use SoftDeletes;

    public $table = 'institutions';

    protected $fillable = [
        'name',
        'image',
        'color',
    ];

    protected $casts = [
        'name' => 'string',
        'color' => 'string',
    ];

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }
}
