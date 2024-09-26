<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class loja_vendas_trocas_produto extends Model
{
    use HasFactory;
    public $table = 'loja_vendas_trocas_produto';
    protected $fillable = ['descricao'];
}
