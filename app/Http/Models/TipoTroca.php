<?php
namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;


class TipoTroca extends Model
{

    public $table = 'loja_tipo_trocas';
    protected $fillable = ['descricao','slug'];
}

