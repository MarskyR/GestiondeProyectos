<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Caja;
use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Proyecto extends Model
{
    protected $table = "proyecto";
    protected $primaryKey ="codProyecto";

    public $timestamps = false;  //para que no trabaje con los campos fecha 


    // le indicamos los campos de la tabla 
    protected $fillable = [ 'codProyecto', 'codigoPresupuestal', 'nombre', 'codEmpleadoDirector', 'codSedePrincipal',
     'nombreLargo', 'codEntidadFinanciera', 'codPEI', 'objetivoGeneral', 'fechaInicio', 'importePresupuestoTotal', 
     'codMonedaPresupuestoTotal', 'importeContrapartidaCedepas', 'codMonedaContrapartidaCedepas', 
     'importeContrapartidaPoblacionBeneficiaria', 'codMonedaContrapartidaPoblacionBeneficiaria', 'importeContrapartidaOtros', 
     'codMonedaContrapartidaOtros', 'codTipoFinanciamiento'];
    

    public function inicializarObjetivosMilenio(){
        $listaObj = ObjetivoMilenio::All();
        foreach ($listaObj as $obj) {
            $relacion = new RelacionProyectoObjMilenio();
            $relacion->porcentaje = 0;
            $relacion->codObjetivoMilenio = $obj->codObjetivoMilenio;
            $relacion->codProyecto = $this->codProyecto;
            $relacion->save();
        }
    }


    public function getCantidadIndicadoresTotales(){//SOLO PARA EXPORTACION DE EXCEL
        $cantidad=0;
        $objetivosEspecificos=ObjetivoEspecifico::where('codProyecto','=',$this->codProyecto)->get();
        foreach ($objetivosEspecificos as $item) {
            $cantidad+=$item->getCantidadIndicadores()+1;
        }
        return $cantidad;
    }
    public function getCantidadIndicadoresResueltosTotales(){//SOLO PARA EXPORTACION DE EXCEL
        $cantidad=0;
        $resultadosEsperados=ResultadoEsperado::where('codProyecto','=',$this->codProyecto)->get();
        foreach ($resultadosEsperados as $item) {
            $cantidad+=$item->getCantidadIndicadoresResultados()+1;
        }
        return $cantidad;
    }
    public function getCantidadActividadesTotales(){//SOLO PARA EXPORTACION DE EXCEL
        $cantidad=0;
        $resultadosEsperados=ResultadoEsperado::where('codProyecto','=',$this->codProyecto)->get();
        foreach ($resultadosEsperados as $item) {
            $cantidad+=$item->getCantidadActividades()+1;
        }
        return $cantidad;
    }


    public function getLugaresEjecucion(){
        return LugarEjecucion::where('codProyecto','=',$this->codProyecto)->get();
    }
    public function getListaPorcentajesObj(){

        return RelacionProyectoObj::where('codProyecto','=',$this->codProyecto)->get();
    }

    public function getRelacionesObjMilenio(){
        return RelacionProyectoObjMilenio::where('codProyecto','=',$this->codProyecto)->get();
    }

    public function getOrigenYNombre(){
        return "[".$this->codigoPresupuestal."] ".$this->nombre;


    }

    public function getPoblacionesBeneficiarias(){

        return PoblacionBeneficiaria::where('codProyecto','=',$this->codProyecto)->get();
    }

    //si el empleado que se manda tiene asignado este proyecto, retorna checked
    public function getCheckedSiTieneContador($codEmpleadoContador){
        if( $this->getTieneAContador($codEmpleadoContador) )
            return "checked";
        
        return "";

    }

    public function getTieneAContador($codEmpleadoContador){
        $lista = ProyectoContador::where('codProyecto','=',$this->codProyecto)
        ->where('codEmpleadoContador','=',$codEmpleadoContador)->get();

        return count($lista) > 0;


    }

    public static function getCodEstado($nombreEstado){
        $lista = EstadoProyecto::where('nombre','=',$nombreEstado)->get();
        if(count($lista)==0)
            throw new Exception("Modelo Proyecto:: getCodEstado, no se encontró un estado con este nombre.", 1);
            
        
        $estado = $lista[0];
        return $estado->codEstadoProyecto;
    }

    /* Si el proyecto no se puede editar, retorna "readonly" */
    public function getReadonly(){
        if(!$this->sePuedeEditar())
            return "readonly";

        return "";
    }

    //se puede editar si esta en estado "En Registro" y la persona que lo esta viendo es UGE
    public function sePuedeEditar(){
        
        return $this->verificarEstado('En Registro') && Empleado::getEmpleadoLogeado()->esUGE(); 

    }
    public function enEjecucion(){
        return $this->verificarEstado('En Ejecucion');
    }

    public function estaFinalizado(){/* En este estado, el proyecto ya no está activo */
        return $this->verificarEstado('Finalizado');
    }
    
    public function estaActivo(){

        return !$this-> estaFinalizado();
        

    }




    public function verificarEstado($estado){

        return $this->getEstado()->nombre == $estado;
    }


    public function getEstado(){
        return EstadoProyecto::findOrFail($this->codEstadoProyecto);

    }

    public function getAñoInicio(){
        return  date('Y', strtotime($this->fechaInicio));
    }

    public function getFechaInicio(){
        return Fecha::formatoParaVistas($this->fechaInicio);
    }
    public function getFechaFinalizacion(){
        return Fecha::formatoParaVistas($this->fechaFinalizacion);
    }

    // le entra string "dias" o "meses" o "años"
    public function getDuracion($tipo,bool $agregado){
        //agregado = true -> retorna la cantidad neta agregada (por ejemplo, 3 años 3 meses 5 días)
        //agregado = false -> retorna las cantidades desagregadas (por ejemplo 3 años, 39 meses, 1190 dias )

        $date1 = new DateTime($this->fechaInicio);
        $date2 = new DateTime($this->fechaFinalizacion);
        $diff = $date1->diff($date2);
        $vectorDif = json_decode(json_encode($diff),true);
        /* diff es:
            {
                "y":1,"m":0,"d":1,"h":0,"i":0,"s":0,"f":0,"weekday":0,"weekday_behavior":0,
            "first_last_day_of":0,"invert":0,"days":367,"special_type":0,"special_amount":0,
            "have_weekday_relative":0,"have_special_relative":0
            }

        */

        switch($tipo){
            case "dias": 
                if($agregado)
                    $retorno = $vectorDif['d'];
                else
                    $retorno = 365*$vectorDif['y'] + 30*$vectorDif['m'] + $vectorDif['d'];;
                break;
            case "meses":
                if($agregado)
                    $retorno = $vectorDif['m'];
                else 
                    $retorno = $vectorDif['y']*12 + $vectorDif['m'];
                break;
            case "años":
                $retorno = $vectorDif['y'];
                break;
        }
        settype($retorno,"integer");

        return $retorno;
    }


    public function getEntidadFinanciera(){
        return EntidadFinanciera::findOrFail($this->codEntidadFinanciera);
    }
    public function getPEI(){
        return PlanEstrategicoInstitucional::findOrFail($this->codPEI);
    }

    public function getMoneda(){
        return Moneda::findOrFail($this->codMoneda);
    }
    public function getTipoFinanciamiento(){
        return TipoFinanciamiento::findOrFail($this->codTipoFinanciamiento);
    }

    public function getSede(){
        return Sede::findOrFail($this->codSedePrincipal);
    }


    public function getIndicadoresResultados(){

        $resultadosEsperados = ResultadoEsperado::where('codProyecto','=',$this->codProyecto)->get();
        $vector = [];
        foreach ($resultadosEsperados as $res) {
            array_push($vector,$res->codResultadoEsperado);
        }

        return IndicadorResultado::whereIn('codResultadoEsperado',$vector)->get();

    }

    //obtiene el siguiente nombre de guardado disponible para un archivo que se creará
    public function getNombreGuardadoNuevoArchivo($indice){
        //FORMATO PARA NOMBRE DE GUARDADO
        //ArchProy-000000-00.marac
        $nombre = "ArchProy-".Debug::rellernarCerosIzq($this->codProyecto,6)."-".Debug::rellernarCerosIzq($indice,2).".marac";
        return $nombre;
        

    }

    public function getListaArchivos(){
        return ArchivoProyecto::where('codProyecto','=',$this->codProyecto)->get();

    }

    public function getCantidadArchivos(){

        return count($this->getListaArchivos());
    }

    //borra los archivos del proyecto 
    public function eliminarArchivos(){

        
        
        foreach ($this->getListaArchivos() as $itemArchivo) {
            $nombre = $itemArchivo->nombreDeGuardado;
            Storage::disk('proyectos')->delete($nombre);
            Debug::mensajeSimple('Se acaba de borrar el archivo:'.$nombre);
        }
        ArchivoProyecto::where('codProyecto','=',$this->codProyecto)->delete();
        
    }


    public function getGerente(){
        return Empleado::findOrFail($this->codEmpleadoDirector);
    }

    public function getNombreCompletoGerente(){
        $empleado=Empleado::where('codEmpleado','=',$this->codEmpleadoDirector)->get();
        if(count($empleado)!=1){
            return "*No definido*";
        }else{
            return $empleado[0]->nombres.' '.$empleado[0]->apellidos;
        }
    }
    
    public function getPorcentajesObjEstrategicos(){
        
        return RelacionProyectoObj::where('codProyecto','=',$this->codProyecto)->get();

    }

    public function eliminarPorcentajesDeObjetivos(){
        RelacionProyectoObj::where('codProyecto','=',$this->codProyecto)->delete();

    }

    public static function getProyectosActivos(){ //FALTA METER EN PROYECTO EL int ACTIVO
        //return Proyecto::All();
        return Proyecto::where('codEstadoProyecto','!=','3')
            ->orderBy('codigoPresupuestal','ASC')
            ->get();
    }

    public function getContadores(){
        $detalles=ProyectoContador::where('codProyecto','=',$this->codProyecto)->get();
        $arr=[];
        foreach ($detalles as $itemdetalle) {
            $arr[]=$itemdetalle->codEmpleadoContador;
        }
        return Empleado::whereIn('codEmpleado',$arr)->get();
    }

    public function nroContadores(){
        $detalles=ProyectoContador::where('codProyecto','=',$this->codProyecto)->get();
        return count($detalles);
    }

    public function evaluador(){
        $empleado=Empleado::find($this->codEmpleadoDirector);
        return $empleado;
    }

    

    public function getResultadosEsperados(){

        return ResultadoEsperado::where('codProyecto','=',$this->codProyecto)->get();

    }


    /* ESTA FUNCION QUE LA HAGA EL FELIX PORQUE YO NO SÉ XD 
    
    retorna un vector de meses con codigoMes (01-12) , año y nombre del mes

    la etapa corresponde al periodo abarcado por
        mes inicio: mes con la meta ejecutada de mes menor
        mes final : mes con la meta ejecutada de mes mayor

    */
    public function getMesesDeEjecucion(){
        
        //METAS EJECUTADAS
        $fechasOcupadas=DB::TABLE('proyecto')
        ->JOIN('resultado_esperado', 'proyecto.codProyecto', '=', 'resultado_esperado.codProyecto')
        ->JOIN('actividad_res', 'resultado_esperado.codResultadoEsperado', '=', 'actividad_res.codResultadoEsperado')
        ->JOIN('indicador_actividad', 'actividad_res.codActividad', '=', 'indicador_actividad.codActividad')
        ->JOIN('meta_ejecutada', 'indicador_actividad.codIndicadorActividad', '=', 'meta_ejecutada.codIndicadorActividad')
        ->SELECT('meta_ejecutada.mesAñoObjetivo as fecha')
        ->where('proyecto.codProyecto','=',$this->codProyecto)
        ->orderBy('meta_ejecutada.mesAñoObjetivo')->get();


        $menor=date("d-m-Y H:i:00",time());
        $mayor="01-01-1900 21:00:00";
        foreach ($fechasOcupadas as $itemfecha) {
            $temp=strtotime($itemfecha->fecha);
            if($temp<strtotime($menor)) $menor=$itemfecha->fecha;
            if($temp>strtotime($mayor)) $mayor=$itemfecha->fecha;
        }


        $añoMenor=intval(date("Y",strtotime($menor)));
        $mesMenor=intval(date("m",strtotime($menor)));
        $añoMayor=intval(date("Y",strtotime($mayor)));
        $mesMayor=intval(date("m",strtotime($mayor)));

        $vector =[];

        $meses=['ENE','FEB','MAR','ABR','MAY','JUN','JUL','AGO','SEP','OCT','NOV','DIC'];
        $bandPrimer=true;
        for ($i=$añoMenor; $i <= $añoMayor; $i++) { 
            $j=1;
            $jLimite=12;

            if($bandPrimer){
                $j=$mesMenor;
                $bandPrimer=false;
            }
            if($i==$añoMayor){
                $jLimite=$mesMayor;
            }

            while($j<=$jLimite){
                $registro = ['codigoMes'=>$j, 'año'=>$i,'nombreMes'=> $meses[$j-1]];
                //$registro = $i.'-'.$j.'-01';
                array_push($vector, $registro);
                $j++;
            }
        }

        /*
        array_push($vector, $menor );
        array_push($vector, $mayor);

        */
        return $vector;


    }
    public function getCantidadColsParaReporteRes(){
        
        /* 
            4 por la columna enunciado y meta , saldo y % ejecucion 
            3 por cada mes tiene prog, ejec y %

        */
        return 4 + count($this->getMesesDeEjecucion())*3;

    }


    public function getListaActividades(){
        $listaResultadosEsperados = ResultadoEsperado::where('codProyecto','=',$this->codProyecto)->get();

        $vector = [];
        foreach ($listaResultadosEsperados as $itemRes) {
            array_push($vector,$itemRes->codResultadoEsperado);
        }

        $listaActividades = ActividadResultado::whereIn('codResultadoEsperado',$vector)->get();
        return $listaActividades;

    }


    //retorna una lista con los contadores del proyecto
    public function getListaContadores(){
        $listaRelaciones = ProyectoContador::where('codProyecto','=',$this->codProyecto)->get();
        $listaContadores = new Collection();
        
        foreach($listaRelaciones as $relacion){
            $empleadoContador = $relacion->getContador();
            $listaContadores->push($empleadoContador);
        }
        return $listaContadores;


    }

    //retorna un string con los tipo archivos que faltan
    public function getTiposArchivosFaltantes(){

        $tiposExistentes = TipoArchivoProyecto::All();
        $vectorTipos = [];
        foreach ($tiposExistentes as $tipo) {
            $vectorTipos[$tipo->codTipoArchivoProyecto] = $tipo->nombre; 
        }
        
        $archivosProyecto = ArchivoProyecto::where('codProyecto','=',$this->codProyecto)->get();

        foreach($archivosProyecto as $ar){
 
            unset($vectorTipos[$ar->codTipoArchivoProyecto]);
        }
        if(count($vectorTipos) == 0)
            return "";

        $cadena = "Faltan los siguientes tipos de archivos: ";
        foreach ($vectorTipos as $value) {
            $cadena.= $value.", ";
        }
    
        $cadena = trim($cadena);
        $cadena = trim($cadena,",");
        $cadena = $cadena.".";
        
        return "*".$cadena;




    }
    /* Retorna una cadena tipo "1,5,7" en la que cada uno es el nro de obj estrategico del PEI  a los que aporta el proy*/
    public function getObjPEIs(){
        $listaRelacion = RelacionProyectoObj::where('codProyecto','=',$this->codProyecto)
            ->where('porcentajeDeAporte','>','0')->get();
        $PEI = PlanEstrategicoInstitucional::findOrFail($this->codPEI);

        $cadena = "";
        foreach ($listaRelacion as $relacion) {
            $objEstr = ObjetivoEstrategico::findOrFail($relacion->codObjetivoEstrategico);
            $cadena.=$objEstr->item.",";

        }
        
        return  trim($cadena,",");
    }

    
    public function getTotalAportesMilenio(){
        $listaRel = RelacionProyectoObjMilenio::where('codProyecto','=',$this->codProyecto)->get();
        $suma = 0;
        foreach($listaRel as $item){
            $suma += $item->porcentaje;
        }
        return $suma;

    }
    public function getTotalAportesEstrategicos(){
        $listaRel = RelacionProyectoObj::where('codProyecto','=',$this->codProyecto)->get();
        $suma = 0;
        foreach($listaRel as $item){
            $suma += $item->porcentajeDeAporte;
        }
        return $suma;

    }


    /* Estadísticas para poblacion beneficiaria 
    Lo hago en SQL Pq en PHP gasto muchos recursos del servidor xxd
    */
    
    /* 
        Retorna la cantidad de personas naturales que figuran como beneficiario
        si hubiera una misma persona en 2 grupos, solo se contará una vez
        
    */
    public function getPersonasNaturalesSinRepetidas(){

        $codProyecto = $this->codProyecto;
        $listaDNIsSinDuplicar = DB::select("select PNP.dni from poblacion_beneficiaria PB
                inner join relacion_personanat_poblacion RPN 
                    on PB.codPoblacionBeneficiaria = RPN.codPoblacionBeneficiaria 
                inner join persona_natural_poblacion PNP 
                    on RPN.codPersonaNatural = PNP.codPersonaNatural
                where codProyecto = $codProyecto
                group by PNP.dni
                order by PNP.dni");

        $arrayDNI = [];
        foreach($listaDNIsSinDuplicar as $pnp){
            $arrayDNI[] = $pnp->dni;
        }
         
        return PersonaNaturalPoblacion::whereIn('dni',$arrayDNI)->get();
         
    }
    public function getPersonasJuridicasSinRepetidas(){

        $codProyecto = $this->codProyecto;
        $listaRUCsSinDuplicar = DB::select(" select PJP.ruc from poblacion_beneficiaria PB
                    inner join relacion_personajur_poblacion RPJ 
                        on PB.codPoblacionBeneficiaria = RPJ.codPoblacionBeneficiaria 
                    inner join persona_juridica_poblacion PJP 
                        on RPJ.codPersonaJuridica = PJP.codPersonaJuridica
                    where codProyecto = $codProyecto
                group by PJP.ruc
                order by PJP.ruc
            ");

       
        $arrayRUC = [];
        foreach($listaRUCsSinDuplicar as $PJP){
            $arrayRUC[] = $PJP->ruc;
        }
         
        return PersonaJuridicaPoblacion::whereIn('ruc',$arrayRUC)->get();
         
    }

    private function getPersonasSinRepetirPorSexo($sexo){
        $codProyecto = $this->codProyecto;
         
        $listaPersonasSinDuplicar = DB::select(
        "SELECT
            PNP.dni,
            PNP.sexo
        FROM
            poblacion_beneficiaria PB
        INNER JOIN relacion_personanat_poblacion RPN ON
            PB.codPoblacionBeneficiaria = RPN.codPoblacionBeneficiaria
        INNER JOIN persona_natural_poblacion PNP ON
            RPN.codPersonaNatural = PNP.codPersonaNatural
        WHERE
            codProyecto = $codProyecto AND PNP.sexo = '$sexo'
        GROUP BY
            PNP.dni
        ORDER BY
            PNP.dni
        ");
        
        $arrayDNI = [];
        foreach($listaPersonasSinDuplicar as $pnp){
            $arrayDNI[] = $pnp->dni;
        }
        
        return PersonaNaturalPoblacion::whereIn('dni',$arrayDNI)->get();
        
 

    }

    public function getMujeresSinRepetir(){
        return $this->getPersonasSinRepetirPorSexo('M');

    }
    
    public function getHombresSinRepetir(){
        return $this->getPersonasSinRepetirPorSexo('H');
    }
    


    
}
