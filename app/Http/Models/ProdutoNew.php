<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdutoNew extends Model
{
    use HasFactory;
    public $table = 'loja_produtos_new';

    public function produtos()
    {
        return $this->belongsTo(ProdutoVariacao::class, 'products_id', 'id');
    }

}

