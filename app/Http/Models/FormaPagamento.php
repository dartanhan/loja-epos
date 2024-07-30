<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormaPagamento extends Model
{
    use HasFactory;
    public $table = 'loja_forma_pagamentos';
    protected $fillable = ['nome','status','slug'];
}
