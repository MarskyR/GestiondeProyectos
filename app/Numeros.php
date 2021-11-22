<?php

namespace App;

class Numeros
{
    

    public static  function escribirNumero($num){
        $n = $num;
        $arr=explode('.', $n);

        if(sizeof($arr)==1){
            $stringDecimal='00';
        }else if(strlen($arr[1])==1){
            $stringDecimal=$arr[1].'0';
        }else{
            $stringDecimal=$arr[1];
        }

        $sinDecimal=intval($arr[0]);
        return Numeros::convertir($sinDecimal).' con '.$stringDecimal.'/100';
        //return Numeros::convertir(Numeros::totalSolicitado);
    }

    static function basico($numero) {
        $valor = array ('uno','dos','tres','cuatro','cinco','seis','siete','ocho',
        'nueve','diez', 'once','doce','trece','catorce','quince','diez y seis','diez y siete','diez y ocho','diez y nueve',
        'veinte','veintiuno','veintidos','veintitres', 'veinticuatro','veinticinco',
        'veintiséis','veintisiete','veintiocho','veintinueve');
        return $valor[$numero - 1];
    }
        
    static function decenas($n) {
        $decenas = array (30=>'treinta',40=>'cuarenta',50=>'cincuenta',60=>'sesenta',
        70=>'setenta',80=>'ochenta',90=>'noventa');
        if( $n <= 29) return Numeros::basico($n);
        $x = $n % 10;
        if ( $x == 0 ) {
            return $decenas[$n];
        } else 
            return $decenas[$n - $x].' y '. Numeros::basico($x);
    }
        
    static function centenas($n) {
        $cientos = array (100 =>'cien',200 =>'doscientos',300=>'trecientos',
        400=>'cuatrocientos', 500=>'quinientos',600=>'seiscientos',
        700=>'setecientos',800=>'ochocientos', 900 =>'novecientos');
        if( $n >= 100) {
            if ( $n % 100 == 0 ) {
                return $cientos[$n];
            } else {
                $u = (int) substr($n,0,1);
                $d = (int) substr($n,1,2);
                return (($u == 1)?'ciento':$cientos[$u*100]).' '.Numeros::decenas($d);
            }
        } else 
            return Numeros::decenas($n);
    }
        
    static function miles($n) {
        if($n > 999) {
            if( $n == 1000) {
                return 'mil';
            }else {
                $l = strlen($n);
                $c = (int)substr($n,0,$l-3); //que es C
                $x = (int)substr($n,-3);    //que es X
                if($c == 1){
                    $cadena = 'mil '.Numeros::centenas($x);
                }else if($x != 0){  //creo que el error está aquí
                    $cadena = Numeros::centenas($c).' mil '.Numeros::centenas($x);
                }else 
                    $cadena = Numeros::centenas($c). ' mil';
                
                return $cadena;
            }
        } else 
            return Numeros::centenas($n);
    }
        
    static function millones($n) {
        if($n == 1000000) {return 'un millón';}
        else {
            $l = strlen($n);
            $c = (int)substr($n,0,$l-6);
            $x = (int)substr($n,-6);
            if($c == 1) {
                $cadena = ' millón ';
            } else {
                $cadena = ' millones ';
            }
            return Numeros::miles($c).$cadena.(($x > 0)?Numeros::miles($x):'');
        }
    }


    static function convertir($n) {
        $sinMayusculas='';

        switch (true) {
            case ( $n >= 1 && $n <= 29) : 
                $sinMayusculas=Numeros::basico($n); break;
            case ( $n >= 30 && $n < 100) : 
                $sinMayusculas=Numeros::decenas($n); break;
            case ( $n >= 100 && $n < 1000) : 
                $sinMayusculas=Numeros::centenas($n); break;
            case ($n >= 1000 && $n <= 999999): 
                $sinMayusculas=Numeros::miles($n); break;
            case ($n >= 1000000): 
                $sinMayusculas=Numeros::millones($n); break;
        }
        return ucfirst($sinMayusculas);
    }
}
