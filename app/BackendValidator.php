<?php

namespace App;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class BackendValidator extends Model 
{
    /* CLASE PARA VALIDAR VALORES DESDE BACKEND 
    
    https://laravel.com/docs/7.x/validation#rule-different


    TODAS LAS CLASES USAN LA VALIDACION DE REQUEST QUE NOS DA LARAVEL, 
        la cual lanza una excepcion si encuentra un error
    
    por lo tanto estas funciones retornaran "" si todo está OK
    y el mensaje de error (de la excepcion) si no se pasó la validacion
    */

    const reglasValidacion = 
    [   
        'SOF' => 
            [
                'Cuerpo' => 
                    [
                        ['justificacion' => 'required|max:'.Configuracion::tamañoMaximoJustificacion],
                        ['girarAOrden' => 'required|max:'.Configuracion::tamañoMaximoGiraraAOrdenDe],
                        ['nroCuenta' => 'required|max:'.Configuracion::tamañoMaximoNroCuentaBanco],
                        ['ComboBoxMoneda'=>'required|min:1|numeric'],
                        ['ComboBoxBanco'=>'required|min:1|numeric'],
                        ['cantElementos'=>'required|min:1|numeric'],
                        ['ComboBoxProyecto'=>'required|min:1|numeric']
                    ],
                'Detalles' => 
                    [
                        'colConcepto' => 'required|max:'.Configuracion::tamañoMaximoConcepto,
                        'colImporte' => 'required|numeric',
                        'colCodigoPresupuestal' => 'required|max:'.Configuracion::tamañoMaximoCodigoPresupuestal
                    ]
            ],
        'REN' => 
            [
                'Cuerpo' => 
                    [

                    ],
                'Detalles' => 
                    [

                    ]
            ]
    ];

    
    public static function validarSOF(Request $request){
        BackendValidator::validarCuerpo($request,'SOF');
        BackendValidator::validarDetalles($request,'SOF',$request->cantElementos);
    }

    public static function validarCuerpo(Request $request,$nombreDocumento){       
        $reglasValidacion = BackendValidator::reglasValidacion[$nombreDocumento]['Cuerpo'];

        foreach ($reglasValidacion as $regla){
            $validator = Validator::make($request->all(),$regla);
            if ($validator->fails()) {
                //Debug::mensajeSimple('hola si'.$validator->);
                throw new Exception("BACKEND VALIDATOR validarCuerpo: Error al validar la regla:".json_encode($regla));
            }
        }
    }
    

    public static function validarDetalles(Request $request,$nombreDocumento,$cantidad){
               
        $reglasValidacion = BackendValidator::getVectorValidacionDetalles($nombreDocumento,$cantidad);

        foreach ($reglasValidacion as $regla){
            $validator = Validator::make($request->all(),$regla);
            if ($validator->fails()) {
                //Debug::mensajeSimple('hola si'.$validator->);
                throw new Exception("BACKEND VALIDATOR validarDetalles: Error al validar la regla:".json_encode($regla));
            }
        }
    }
    

    /* Esta funcion genera un vector de este tipo
        ['colConcepto0' => 'required|max:5000'],
        ['colImporte0' => 'required|numeric'],
        ['colCodigoPresupuestal0' => 'required|max'],

        ['colConcepto1' => 'required|max:5000'],
        ['colImporte1' => 'required|numeric'],
        ['colCodigoPresupuestal1' => 'required|max'],

        ['colConcepto2' => 'required|max:5000'],
        ['colImporte2' => 'required|numeric'],
        ['colCodigoPresupuestal2' => 'required|max'],
    */
    public static function getVectorValidacionDetalles($nombreDocumento,$cantidad){
        $vectorValidacion = [];
        $listaReglas = BackendValidator::reglasValidacion[$nombreDocumento]['Detalles'];
        $listaNombresCampos = array_keys($listaReglas);
        
        for ($i=0; $i < $cantidad; $i++) { 
            /*
            $reglasItem = [
                ['colConcepto'.$i => $listaReglas['colConcepto']] ,
                ['colImporte'.$i => $listaReglas['colImporte'] ],
                ['colCodigoPresupuestal'.$i => $listaReglas['colCodigoPresupuestal']]
            ]; 
            */
            $reglasItem=[];
            foreach ($listaNombresCampos as $campo){ //añadimos una regla
                $miniVector = [ $campo.$i => $listaReglas[$campo] ];
                array_push($reglasItem, $miniVector);
            }
            $vectorValidacion = array_merge($vectorValidacion,$reglasItem);
        }
        //Debug::mensajeSimple('Vector de validacion de detalles:'.json_encode($vectorValidacion));
        return $vectorValidacion;
        
    }


    

}
