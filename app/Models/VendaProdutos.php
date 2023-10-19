<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendaProdutos extends Model
{
    use HasFactory;
    public $table = 'loja_venda_produtos';
    protected $fillable = ['codigo_produto','descricao','valor_produto','quantidade','venda_id'];
}
