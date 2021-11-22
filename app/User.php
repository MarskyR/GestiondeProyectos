<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens,Notifiable;
    public $table = "usuario";
    protected $primaryKey = 'codUsuario';
    public $timestamps = false;

    protected $fillable = [
        'usuario', 'email', 'password',
    ];
    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */

    public function empleado(){//singular pq un producto es de una cateoria
        return $this->hasOne('App\Empleado','codUsuario','codUsuario');//el tercer parametro es de Producto
    }

    public function getEmpleado(){
        return Empleado::where('codUsuario','=',$this->codUsuario)->first();
    }

    /* busca un usuario, si encuentra uno retorna el objeto usuario. si no retorna "" */
    public static function buscarPorUsuario($nombreUsuario){
        $lista = User::where('usuario','=',$nombreUsuario)->get();

        if( count($lista) == 0 )
            return "";

        return $lista[0];

    }

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
