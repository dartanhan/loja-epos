<?php
namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class VendasCashBack extends Model
{
    public $table = 'loja_vendas_cashback';
    protected $fillable = ['cliente_id','venda_id','valor','created_at','update_at'];

     // Define o relacionamento belongsTo
     public function vendas()
     {
         return $this->belongsTo(Vendas::class, 'venda_id');
     }

     public function cliente(){
         return $this->belongsTo(Cliente::class, 'cliente_id');
     }

    public function cart()
    {
        return $this->belongsTo(Carts::class);
    }
}
