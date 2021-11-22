<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mes extends Model
{
    public $timestamps = false;

    protected $table = 'mes';

    protected $primaryKey = 'codMes';
    
    protected $fillable = ['nombre','abreviacion'];


    
}
