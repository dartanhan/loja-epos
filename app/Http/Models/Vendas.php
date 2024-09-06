<?php
namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static join(string $string, string $string1, string $string2, string $string3)
 */
class Vendas extends Model
{
    public $table = 'loja_vendas';
    protected $fillable = ['codigo_venda','loja_id','valor_total','troca','cliente_id','tipo_venda_id','usuario_id','created_at','created_at'];

    function vendas() {
        return  $this->hasMany(VendaProdutos::class);
    }

    function products(){
        return $this->hasMany(VendaProdutos::class, 'venda_id', 'id');
    }

    function cliente(){
        return $this->hasMany(Cliente::class, 'id', 'cliente_id');
    }

    function forma_pgto(){
        return $this->hasMany(VendasProdutosTipoPagamento::class, 'venda_id', 'id');
    }

    function desconto(){
        return $this->hasMany(VendasProdutosDesconto::class, 'venda_id', 'id');
    }

    function cashback(){
        return $this->hasMany(VendasCashBack::class, 'venda_id', 'id');
    }

    function frete(){
        return $this->hasMany(VendasProdutosEntrega::class, 'venda_id', 'id');
    }
}
