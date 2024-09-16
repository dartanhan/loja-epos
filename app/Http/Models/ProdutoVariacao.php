<?php

namespace App\Http\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdutoVariacao extends Model
{
    use HasFactory;
    public $table = 'loja_produtos_variacao';
    protected $fillable = ['id','products_id','subcodigo','variacao','valor_varejo','valor_atacado','valor_atacado_5un','valor_atacado_10un','valor_lista','valor_produto'
        ,'percentage','quantidade','quantidade_minima','status','validade','created_at','fornecedor','estoque','valor_cartao_pix','valor_parcelado'];

    function images(){
        return  $this->hasMany(ProdutoImagem::class ,'produto_variacao_id','id');
    }

    function produtos(){
        return  $this->hasMany(ProdutoNew::class ,'id', 'products_id');
    }


}
