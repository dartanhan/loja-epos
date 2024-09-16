<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    public $table = 'loja_clientes';
    protected $fillable = ['cpf','telefone','nome','cep','logradouro','numero','complemento','bairro','localidade','uf','taxa','email','created_at','update_at'];

    public function carts() {
        return $this->belongsTo(Carts::class);
    }


}
