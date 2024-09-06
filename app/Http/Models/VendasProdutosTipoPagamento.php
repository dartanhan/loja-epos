<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class VendasProdutosTipoPagamento extends Model
{
    public $table = 'loja_vendas_produtos_tipo_pagamentos';
    public $timestamps = false;
    protected $fillable = ['venda_id','forma_pagamento_id','valor_pgto','taxa'];

     public function payments()
     {
         return $this->belongsTo(Payments::class, 'forma_pagamento_id', 'id');
     }

    public function PaymentsTaxes()
    {
        return $this->hasMany( TaxaCartao::class,'forma_id','id');
    }

}
