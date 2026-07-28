<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GenovaProduct extends Model
{
    protected $primaryKey = 'esu_product_id';
    public $incrementing = false;
    protected $keyType = 'int';
    
    protected $fillable = [
        'esu_product_id',
        'esu_main_product_id',
        'name'
    ];
}
