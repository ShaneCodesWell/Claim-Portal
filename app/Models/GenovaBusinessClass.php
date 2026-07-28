<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GenovaBusinessClass extends Model
{
    protected $primaryKey = 'esu_main_product_id';
    public $incrementing = false;
    protected $keyType = 'int';
    
    protected $fillable = [
        'esu_main_product_id',
        'name'
    ];
}
