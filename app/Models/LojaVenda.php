<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LojaVenda extends Model
{
    use HasFactory;
    public $table = 'loja_vendas';
    protected $fillable = ['codigo_venda'];
}
