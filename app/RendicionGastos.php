<?php

namespace App;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Fecha;

use Illuminate\Support\Collection;
class RendicionGastos extends DocumentoAdministrativo
{
    protected $table = "rendicion_gastos";
    protected $primaryKey ="codRendicionGastos";

    const raizArchivo = "RendGast-CDP-";
    const RaizCodigoCedepas = "REN";
    const codTipoDocumento = "2";

    public $timestamps = false;  //para que no trabaje con los campos fecha 


    // le indicamos los campos de la tabla 
    protected $fillable = ['codSolicitud','codigoCedepas','totalImporteRecibido',
    'totalImporteRendido','saldoAFavorDeEmpleado',
    'resumenDeActividad','codEstadoRendicion','fechaHoraRendicion',
    'cantArchivos','terminacionesArchivos','codEmpleadoEvaluador'];

    //esto es para el historial de operaciones
    public function getVectorDocumento(){
        return [
            'codTipoDocumento' => RendicionGastos::codTipoDocumento,
            'codDocumento' => $this->codRendicionGastos
        ];
    }

    public function getDetalles(){
        return DetalleRendicionGastos::
            where('codRendicionGastos','=',$this->codRendicionGastos)
            ->get();
    }

    /** FORMATO PARA FECHAS*/
    public function formatoFechaHoraRendicion(){
        if(is_null($this->fechaHoraRendicion))
            return "";
        
        $fecha=date('d/m/Y H:i:s', strtotime($this->fechaHoraRendicion));
        return $fecha;
    }

 
    public function formatoFechaHoraRevisionGerente(){

        if(is_null($this->fechaHoraRevisado))
            return "";

        $fecha=date('d/m/Y H:i:s', strtotime($this->fechaHoraRevisado));
        return $fecha;
    }

 
    
    //si está en esos estados retorna la obs, sino retorna ""
    public function getObservacionONull(){
        if($this->verificarEstado('Observada') || $this->verificarEstado('Subsanada') )
            return ": ".$this->observacion;
        
        return "";
    }

    function getFechaHoraRendicion(){
         
        return str_replace('-','/',$this->fechaHoraRendicion);

    }

    function getFechaHoraRevisado(){
        return str_replace('-','/',$this->fechaHoraRevisado);


    }

    /* AQUI AÑADIR CODIGOS RGB DE LA MARSKY */
    function getColorSaldo(){
        if($this->getSaldoFavorCedepas()>0)
            return "rgb(0, 167, 14)";
        else
            return "red";
    }


    public static function calcularCodigoCedepas($objNumeracion){
        return  RendicionGastos::RaizCodigoCedepas.
                substr($objNumeracion->año,2,2).
                '-'.
                RendicionGastos::rellernarCerosIzq($objNumeracion->numeroLibreActual,6);
    }
  

    public function borrarArchivosCDP(){ //borra todos los archivos que sean de esa rendicion
        foreach ($this->getListaArchivos() as $itemArchivo) {
            $nombre = $itemArchivo->nombreDeGuardado;
            Storage::disk('rendiciones')->delete($nombre);
            Debug::mensajeSimple('Se acaba de borrar el archivo:'.$nombre);
        } 
        return ArchivoRendicion::where('codRendicionGastos','=',$this->codRendicionGastos)->delete();

    }

    public function getListaArchivos(){

        return ArchivoRendicion::where('codRendicionGastos','=',$this->codRendicionGastos)->get();
    }

    public function getCantidadArchivos(){
        return count($this->getListaArchivos());

    }



    //               RendGast-CDP-   000002                           -   5   .  jpg
    public static function getFormatoNombreCDP($codRendicionGastos,$i,$terminacion){
        return  RendicionGastos::raizArchivo.
                RendicionGastos::rellernarCerosIzq($codRendicionGastos,6).
                '-'.
                RendicionGastos::rellernarCerosIzq($i,2).
                '.'.
                $terminacion;
    }


    public function getNombreGuardadoNuevoArchivo($j){
        return  RendicionGastos::raizArchivo.
        RendicionGastos::rellernarCerosIzq($this->codRendicionGastos,6).
        '-'.
        RendicionGastos::rellernarCerosIzq($j,2).
        '.marac';
    }

    //retorna vector de strings 
    public function getVectorTerminaciones(){
        return explode('/',$this->terminacionesArchivos);
    }

    //la primera es la 1 OJO
    public function getTerminacionNro($index){
        $vector = explode('/',$this->terminacionesArchivos);
        return $vector[$index-1];

    }

    public static function rellernarCerosIzq($numero, $nDigitos){
        return str_pad($numero, $nDigitos, "0", STR_PAD_LEFT);
 
     }
    

    public function getPDF(){
        $listaItems = DetalleRendicionGastos::where('codRendicionGastos','=',$this->codRendicionGastos)->get();
        
        
        $pdf = \PDF::loadview('RendicionGastos.PdfRendicionGastos',
            array('rendicion'=>$this,'listaItems'=>$listaItems)
                            )->setPaper('a4', 'portrait');
        return $pdf;
    }

    public function getNombreEvaluador(){
        if(is_null($this->codEmpleadoEvaluador) )
            return "";
        
        $ev = Empleado::findOrFail($this->codEmpleadoEvaluador);
        return $ev->getNombreCompleto();
    }


    /* Retorna el codigo del estado indicado por el str parametro */
    public static function getCodEstado($nombreEstado){
        $lista = EstadoRendicionGastos::where('nombre','=',$nombreEstado)->get();
        if(count($lista)==0)
            return 'Nombre no valido';
        
        return $lista[0]->codEstadoRendicion;

    }

    public function getEstado(){
        return EstadoRendicionGastos::findOrFail($this->codEstadoRendicion);
    }

    public function setEstado($codEstado){
        $this->codEstadoRendicion = $codEstado;
        $this->save();

    }
    

    /* Retorna TRUE or FALSE cuando le mandamos el nombre de un estado */
    public function verificarEstado($nombreEstado){
        $lista = EstadoRendicionGastos::where('nombre','=',$nombreEstado)->get();
        if(count($lista)==0)
            return false;
        $estado = $lista[0];
        if($estado->codEstadoRendicion == $this->codEstadoRendicion)
            return true;
        
        return false;
        
    }

    public function listaParaAprobar(){
        return $this->verificarEstado('Creada') ||
        $this->verificarEstado('Subsanada'); 

    }
    


    public function listaParaActualizar(){
        return $this->verificarEstado('Creada') || 
        $this->verificarEstado('Observada') || 
        $this->verificarEstado('Subsanada'); 

    }

    
    public function listaParaObservar(){
        return $this->verificarEstado('Creada') || 
        $this->verificarEstado('Aprobada') || 
        $this->verificarEstado('Subsanada') 
        ; 

    }

    public function listaParaContabilizar(){
        return $this->verificarEstado('Aprobada');

    }

    public function getProyecto(){
       
        return $this->getSolicitud()->getProyecto();

    }


    /* Como todo el sistema de rendiciones lo hice con el paradigma de que el 
    saldo a favor del empleado debia mostrarse y ya hay datos en la BD,
    ahora que me dicen que en realidad es el a favor de cedepas, usaré esta funcion para des hacer la resta 
    
    */
    public function getSaldoFavorCedepas(){
        return (-1)*$this->saldoAFavorDeEmpleado;

    }

    public function getNombreSolicitante(){
        return $this->getSolicitud()->getNombreSolicitante();
    }
    public function getEmpleadoSolicitante(){
        return $this->getSolicitud()->getEmpleadoSolicitante();
    }

    public function getNombreProyecto(){
        return $this->getSOlicitud()->getNombreProyecto();
    }



    //VER EXCEL https://docs.google.com/spreadsheets/d/1eBQV5QZJ6dTlFtu-PuF3i71Cjg58DIbef2qI0ZqKfoI/edit#gid=1819929291
    public function getNombreEstado(){ 
        $estado = EstadoRendicionGastos::findOrFail($this->codEstadoRendicion);
        if($estado->nombre=="Creada")
            return "Por Aprobar";
        return $estado->nombre;

    }





    //ingresa una coleccion y  el codEstadoSolicitud y retorna otra coleccion  con los elementos de esa coleccion que están en ese estado
    public static function separarDeColeccion($coleccion, $codEstadoRendicion){
        $listaNueva = new Collection();
        foreach ($coleccion as $item) {
            if($item->codEstadoRendicion == $codEstadoRendicion)
                $listaNueva->push($item);
        }
        return $listaNueva;
    }

    
    

    // Observadas -> Subsanadas-> creadas -> Aprobadas -> Contabilizadas -> rechazadas -> canceladas
    public static function ordenarParaEmpleado($coleccion){
        
        $observadas = RendicionGastos::separarDeColeccion($coleccion,RendicionGastos::getCodEstado('Observada'));
        $subsanada = RendicionGastos::separarDeColeccion($coleccion,RendicionGastos::getCodEstado('Subsanada')); 
        $creadas = RendicionGastos::separarDeColeccion($coleccion,RendicionGastos::getCodEstado('Creada')); 
        $aprobadas = RendicionGastos::separarDeColeccion($coleccion,RendicionGastos::getCodEstado('Aprobada')); 

        $contabilizadas = RendicionGastos::separarDeColeccion($coleccion,RendicionGastos::getCodEstado('Contabilizada')); 
        $canceladas = RendicionGastos::separarDeColeccion($coleccion,RendicionGastos::getCodEstado('Cancelada')); 
        $rechazadas = RendicionGastos::separarDeColeccion($coleccion,RendicionGastos::getCodEstado('Rechazada')); 

        $listaOrdenada = new Collection();
        $listaOrdenada= $listaOrdenada->concat($observadas);
        $listaOrdenada= $listaOrdenada->concat($subsanada);
        $listaOrdenada= $listaOrdenada->concat($creadas);
        $listaOrdenada= $listaOrdenada->concat($aprobadas);

        $listaOrdenada= $listaOrdenada->concat($contabilizadas);
        $listaOrdenada= $listaOrdenada->concat($rechazadas);
        $listaOrdenada= $listaOrdenada->concat($canceladas);
        

        return $listaOrdenada;

    }
    
    
    //Creadas -> Subsanadas -> Aprobadas -> Contabilizadas
    public static function ordenarParaGerente($coleccion){
        
        $subsanada = RendicionGastos::separarDeColeccion($coleccion,RendicionGastos::getCodEstado('Subsanada')); 
        $creadas = RendicionGastos::separarDeColeccion($coleccion,RendicionGastos::getCodEstado('Creada'));
        $observadas = RendicionGastos::separarDeColeccion($coleccion,RendicionGastos::getCodEstado('Observada'));
        
        $aprobadas = RendicionGastos::separarDeColeccion($coleccion,RendicionGastos::getCodEstado('Aprobada')); 
        $contabilizadas = RendicionGastos::separarDeColeccion($coleccion,RendicionGastos::getCodEstado('Contabilizada')); 
        
        $listaOrdenada = new Collection();

        $listaOrdenada= $listaOrdenada->concat($subsanada);
        $listaOrdenada= $listaOrdenada->concat($creadas);
        $listaOrdenada= $listaOrdenada->concat($observadas);
        
        $listaOrdenada= $listaOrdenada->concat($aprobadas);
        $listaOrdenada= $listaOrdenada->concat($contabilizadas);
   
        

        return $listaOrdenada;

    }

    
    public static function ordenarParaContador($coleccion){
        
        $aprobadas = RendicionGastos::separarDeColeccion($coleccion,RendicionGastos::getCodEstado('Aprobada')); 
        $contabilizadas = RendicionGastos::separarDeColeccion($coleccion,RendicionGastos::getCodEstado('Contabilizada')); 

        $listaOrdenada = new Collection();

        $listaOrdenada= $listaOrdenada->concat($aprobadas);
        $listaOrdenada= $listaOrdenada->concat($contabilizadas);
   
        

        return $listaOrdenada;

    }

    











    public function getMensajeEstado(){
        $mensaje = '';
        switch($this->codEstadoRendicion){
            case $this::getCodEstado('Creada'): 
                $mensaje = 'La rendición está a espera de ser aprobada por el responsable del proyecto.';
                break;
            case $this::getCodEstado('Aprobada'):
                $mensaje = 'La rendición está a espera de ser contabilizada.';
                break;
            case $this::getCodEstado('Contabilizada'):
                $mensaje = 'El flujo de la Rendición ha finalizado.';
                break;
            case $this::getCodEstado('Observada'):
                $mensaje ='La rendición tiene algún error y fue observada.';
                break;
            case $this::getCodEstado('Subsanada'):
                $mensaje ='La observación de la rendición ya fue corregida por el empleado.';
                break;
            case $this::getCodEstado('Rechazada'):
                $mensaje ='La rendición fue rechazada por algún responsable, el flujo ha terminado.';
                break;
            case $this::getCodEstado('Cancelada'):
                $mensaje ='La rendición fue cancelada por el mismo empleado que la realizó.';
                break;
        }
        return $mensaje;


    }


    public function getColorEstado(){ //BACKGROUND
        $color = '';
        switch($this->codEstadoRendicion){
            case $this::getCodEstado('Creada'): 
                $color = 'rgb(255,193,7)';
                break;
            case $this::getCodEstado('Aprobada'):
                $color = 'rgb(0,154,191)';
                break;
            case $this::getCodEstado('Contabilizada'):
                $color = 'rgb(40,167,69)';
                break;
            case $this::getCodEstado('Observada'):
                $color ='rgb(244,246,249)';
                break;
            case $this::getCodEstado('Subsanada'):
                $color ='rgb(68,114,196)';
                break;
            case $this::getCodEstado('Rechazada'):
                $color ='rgb(192,0,0)';
                break;
                                    
            
        }
        return $color;
    }

    public function getColorLetrasEstado(){
        $color = '';
        switch($this->codEstadoRendicion){
            case $this::getCodEstado('Creada'): 
                $color = 'black';
                break;
            case $this::getCodEstado('Aprobada'):
                $color = 'white';
                break;
            case $this::getCodEstado('Contabilizada'):
                $color = 'white';
                break;
            case $this::getCodEstado('Observada'):
                $color = 'black';
                break;
            case $this::getCodEstado('Subsanada'):
                $color ='white';
                break;
            case $this::getCodEstado('Rechazada'):
                $color ='white';
                break;
        }
        return $color;
    }

    public function getMoneda(){
        return $this->getSolicitud()->getMoneda();
    }

    public function getSolicitud(){
        return SolicitudFondos::findOrFail($this->codSolicitud);

    }

    public static function reportePorSedes($fechaI, $fechaF){
        $listaX = DB::select('
        select sede.nombre as "Sede", SUM(RG.totalImporteRendido) as "Suma_Sede"
        from rendicion_gastos RG
            inner join solicitud_fondos USING(codSolicitud)
            inner join sede USING(codSede)
            where RG.fechaHoraRendicion > "'.$fechaI.'" and RG.fechaHoraRendicion < "'.$fechaF.'" 
            GROUP BY sede.nombre;
        ');
        return $listaX;

    }
    


    public static function reportePorEmpleados($fechaI, $fechaF){
        $listaX = DB::select('
                    select E.nombres as "NombreEmp", SUM(RG.totalImporteRendido) as "Suma_Empleado"
                        from rendicion_gastos RG
                            inner join solicitud_fondos SF USING(codSolicitud)
                            inner join empleado E on E.codEmpleado = SF.codEmpleadoSolicitante 
                            where RG.fechaHoraRendicion > "'.$fechaI.'" and RG.fechaHoraRendicion < "'.$fechaF.'" 
                            GROUP BY E.nombres;
                            ');
        return $listaX;
         

    } 
    public static function reportePorProyectos($fechaI, $fechaF){
        
        $listaX = DB::select('
        select P.nombre as "NombreProy", SUM(RG.totalImporteRendido) as "Suma_Proyecto"
        from rendicion_gastos RG
            inner join solicitud_fondos SF USING(codSolicitud)
            inner join proyecto P on P.codProyecto = SF.codProyecto 
            where RG.fechaHoraRendicion > "'.$fechaI.'" and RG.fechaHoraRendicion < "'.$fechaF.'" 
            GROUP BY P.nombre;
            ');

        return $listaX;

    }


    public static function reportePorSedeYEmpleados($fechaI, $fechaF, $idSede){
        $sede = Sede::findOrFail($idSede);
        $listaX = DB::select('
        select E.nombres as "NombreEmp", SUM(RG.totalImporteRendido) as "Suma_Empleado"
            from rendicion_gastos RG
                inner join solicitud_fondos SF USING(codSolicitud)
                inner join empleado E on E.codEmpleado = SF.codEmpleadoSolicitante 
                where RG.fechaHoraRendicion > "'.$fechaI.'" and RG.fechaHoraRendicion < "'.$fechaF.'" 
                and SF.codSede = "'.$sede->codSede.'"
                GROUP BY E.nombres;
                ');     
        return $listaX;

    }

    /**ESCRIBIR NUMEROSSSSS */
    function escribirImporteRecibido(){
        return Numeros::escribirNumero($this->totalImporteRecibido);
    }
    function escribirImporteRendido(){
        return Numeros::escribirNumero($this->totalImporteRendido);
    }




    /* Convierte el objeto en un vector con elementos leibles directamente por la API */
    public function getVectorParaAPI(){
        $itemActual = $this;
        $solicitud = $this->getSolicitud();

        $itemActual['codigoYproyecto'] = $solicitud->getProyecto()->getOrigenYNombre()  ;
        $itemActual['montoRendido'] = $this->getSolicitud()->getMoneda()->simbolo." ".$this->totalImporteRendido ;
        $itemActual['nombreEstado'] = $this->getEstado()->nombre;
        $itemActual['nombreEmisor'] = $this->getEmpleadoSolicitante()->getNombreCompleto();
        $itemActual['codigoEmpleadoEmisor'] = $this->getEmpleadoSolicitante()->codigoCedepas;
        

        $itemActual['codigoCedepasSolicitud'] = $this->getSolicitud()->codigoCedepas;
        $itemActual['colorFondo'] = $this->getColorEstado();
        $itemActual['colorLetras'] = $this->getColorLetrasEstado();
        $itemActual['simboloMoneda'] = $solicitud->getMoneda()->simbolo;
        $itemActual['fechaHoraRendicion'] = $this->formatoFechaHoraRendicion();
        
        $itemActual['montoRecibido'] = $this->getSolicitud()->getMoneda()->simbolo." ".$this->totalImporteRecibido ;
        

        return $itemActual;
    }

    public function getDetallesParaAPI(){
        $listaDetalles = $this->getDetalles();
        $listaPreparada = [];
        foreach ($listaDetalles as $det) {
            $listaPreparada[] = $det->getVectorParaAPI();
        }
        return $listaPreparada;
    }

}
