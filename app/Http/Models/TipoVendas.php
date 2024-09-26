<?php
namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;


class TipoVendas extends Model
{
    public $table = 'loja_tipo_vendas';
    protected $fillable = ['descricao','slug'];

}
