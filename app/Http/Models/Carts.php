<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carts extends Model
{
    use HasFactory;
    public $table = "loja_carts";
    protected $fillable = ['user_id','cliente_id','produto_variation_id','name','price','quantidade','imagem','status'];

    public function variations(){
        return $this->hasMany(ProdutoVariation::class, 'id', 'produto_variation_id');
    }

    public function clientes(){
        return $this->hasMany(Cliente::class, 'id', 'cliente_id');
    }
}
