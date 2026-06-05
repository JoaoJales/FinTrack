<?php

namespace App\Models;

use Database\Factories\InstitutionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
    /** @use HasFactory<InstitutionFactory> */
    use HasFactory, SoftDeletes;

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

    /**
     * Logos multicoloridas (com branco ou várias cores) não devem usar brightness-0 invert.
     *
     * @var list<string>
     */
    private const MULTICOLOR_LOGOS = [
        'banks-logos/mercado-pago.svg',
        'banks-logos/banrisul-logo.svg',
    ];

    public function logoUsesWhiteFilter(): bool
    {
        return ! in_array($this->image, self::MULTICOLOR_LOGOS, true);
    }
}
