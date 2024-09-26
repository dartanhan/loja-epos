<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class loja_status_troca extends Model
{
    use HasFactory;
    public $table = 'loja_status_troca';
    protected $fillable = ['descricao'];
}
