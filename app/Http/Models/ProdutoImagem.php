<?php

namespace App\Http\Models;

use App\http\Models\ProdutoVariacao;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static select(string $string, string $string1, string $string2)
 */
class ProdutoImagem extends Model
{
    public $table = 'loja_produtos_imagens';
    protected $fillable = ['produto_variacao_id','path'];

    public function images() {
        return $this->belongsTo(ProdutoVariacao::class);
    }
}
