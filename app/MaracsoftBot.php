<?php
namespace App;

use App\Debug;

class MaracsoftBot {

    const tokenBot = "1856544579:AAFFp1cFdLpwTuNZlZDIa7tJu3XDLi27bk4";

    //IDS de telegram
    const idCanalLogsProduccion = "-539429585";
    const idUsuarioDiego = "1448599566";
    const idCanalLogsPruebas = "-467002205";
    const idCanalBackups = "-465250581";
    
    
    private static function getURLBase(){
        return "https://api.telegram.org/bot".MaracsoftBot::tokenBot;

    }

    
    public static function getURLArchivos(){
        return MaracsoftBot::getURLBase()."/sendDocument"; 
    }

    public static function getURLMensajes(){
        return MaracsoftBot::getURLBase()."/sendMessage"; 
    }

    /* Envia un mensaje al canal de maracsoft */
    public static function enviarMensaje($msg){

        if(Configuracion::direccionDelMensaje)
            $idDestino = MaracsoftBot::idCanalLogsProduccion;
        else 
            $idDestino = MaracsoftBot::idCanalLogsPruebas;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, MaracsoftBot::getURLMensajes());
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "chat_id={$idDestino}&parse_mode=HTML&text=$msg");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        Debug::mensajeSimple("RESPUESTA DE TELEGRAM API: " . $server_output);
        curl_close($ch);

    }


    /* Envia un archivo */
    public function enviarArchivo($rutaArchivo,$nombreAparente,$mensajeConElArchivo){
        
        $idDestino = MaracsoftBot::idCanalBackups;

        
        $post = array( 'chat_id' => $idDestino,
                    'caption' => $mensajeConElArchivo ,
                    'document' => new \CurlFile($rutaArchivo, 'application/octet-stream', $nombreAparente)
                    ); 

        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, MaracsoftBot::getURLArchivos()); 
        curl_setopt($ch, CURLOPT_POST, 1); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: multipart/form-data'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        $server_output = curl_exec($ch); 
        Debug::mensajeSimple("RESUPUESTA DEL SERVIDOR:");
        Debug::mensajeSimple(json_encode($server_output));

        curl_close($ch); 


    }

    
}