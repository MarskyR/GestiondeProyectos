<?php

namespace App;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Throwable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\User;

class Empleado extends Model
{
    protected $table = "empleado";
    protected $primaryKey ="codEmpleado";

    public $timestamps = false;  //para que no trabaje con los campos fecha 

    
    // le indicamos los campos de la tabla 
    protected $fillable = ['codUsuario','nombres','apellidos','activo','codigoCedepas','dni'
    ,'codPuesto','fechaRegistro','fechaDeBaja','codSede'
    ];


    public function getSedeQueAdministra(){
        $lista = Sede::where('codEmpleadoAdministrador','=',$this->codEmpleado)->get();
        if(count($lista)==0)
            return new Sede();

        return $lista[0];
    }

    /* busca un empleado, si encuentra uno retorna el objeto empleado. si no retorna "" */
    public static function buscarPorDNI($DNI){
        $lista = Empleado::where('dni','=',$DNI)->get();

        if( count($lista) == 0 )
            return "";

        return $lista[0];

    }

    public function getSolicitudesPorRendir(){
        $vector = [SolicitudFondos::getCodEstado('Abonada'),SolicitudFondos::getCodEstado('Contabilizada')];

        return SolicitudFondos::whereIn('codEstadoSolicitud',$vector)
        ->where('codEmpleadoSolicitante','=',$this->codEmpleado)
        ->where('estaRendida','=',0)
        ->get();


    }

    
    public function getSolicitudesObservadas(){
        return SolicitudFondos::
            where('codEstadoSolicitud','=',SolicitudFondos::getCodEstado('Observada'))
            ->where('codEmpleadoSolicitante','=',$this->codEmpleado)
            ->get();


    }


    public function getReposicionesObservadas(){

        return ReposicionGastos::where('codEmpleadoSolicitante','=',$this->codEmpleado)
            ->where('codEstadoReposicion','=',ReposicionGastos::getCodEstado('Observada'))
            ->get();


    }
    public function getRequerimientosObservados(){

        return RequerimientoBS::where('codEmpleadoSolicitante','=',$this->codEmpleado)
            ->where('codEstadoRequerimiento','=',RequerimientoBS::getCodEstado('Observada'))
            ->get();


    }
    public function getRendicionesObservadas(){

        return RendicionGastos::where('codEmpleadoSolicitante','=',$this->codEmpleado)
            ->where('codEstadoRendicion','=',RendicionGastos::getCodEstado('Observada'))
            ->get();


    }



    public function getDetallesPendientesRendicion(){
        $rendicionesDelEmpleado = RendicionGastos::where('codEmpleadoSolicitante','=',$this->codEmpleado)
            ->get();
        $vectorDeCodsRendicion = [];

        foreach ($rendicionesDelEmpleado as $item) {
            array_push($vectorDeCodsRendicion,$item->codRendicionGastos);
            
        }
        //Debug::mensajeSimple(implode(' , ',$vectorDeCodsRendicion));
        
        
        
        $lista =  DetalleRendicionGastos::
            whereIn('codRendicionGastos',$vectorDeCodsRendicion)
            ->where('pendienteDeVer','=','1')
            ->get();
        
        return $lista;
    }
    public function getDetallesPendientesReposicion(){
        $reposicionesDelEmpleado = ReposicionGastos::where('codEmpleadoSolicitante','=',$this->codEmpleado)
            ->get();
        $vectorDeCodsReposicion = [];

        foreach ($reposicionesDelEmpleado as $item) {
            array_push($vectorDeCodsReposicion,$item->codReposicionGastos);
            
        }
        //Debug::mensajeSimple(implode(' , ',$vectorDeCodsRendicion));
        
        $lista =  DetalleReposicionGastos::
            whereIn('codReposicionGastos',$vectorDeCodsReposicion)
            ->where('pendienteDeVer','=','1')
            ->get();
        
        return $lista;
    }


    public function getNombrePuesto(){
        $cad = $this->getPuestoActual()->nombre;
        if($cad == 'Empleado')
            $cad = "Colaborador";

        return $cad;

    }




    //le pasamos la id del usuario y te retorna el codigo cedepas del empleado
    public function getNombrePorUser( $idAuth){
        $lista = Empleado::where('codUsuario','=',$idAuth)->get();
        return $lista[0]->nombres;

    } 

    public function esGerente(){
        $puesto = Puesto::findOrFail($this->codPuesto);
        if($puesto->nombre=='Gerente')//si es gerente
            return true;

        return false;

    }

    public function esContador(){
        $puesto = Puesto::findOrFail($this->codPuesto);
        if($puesto->nombre=='Contador')//si es gerente
            return true;

        return false;

    }
    
    /* Retorna el obj sede si el empleado es contador */
    public function getSedeContador(){
        return Sede::findOrFail($this->codSedeContador);
    }

    public function getSedeContadorOAdministrador(){
        if($this->esContador())
            return $this->getSedeContador();

        if($this->esJefeAdmin())
            return $this->getSedeQueAdministra();

    }

    public function esAdminSistema(){
        $usuario = User::findOrFail($this->codUsuario);
        return $usuario->isAdmin=='1';

    }

    public function esUGE(){
        $puesto = Puesto::findOrFail($this->codPuesto);
        if($puesto->nombre=='UGE')//si es el actor UGE
            return true;
        return false;
    }
    
    /* REFACTORIZAR ESTO PARA LA NUEVA CONFIGURACION DEL A BASE DE DATOOOOOOOOOOOOOOOOOOOOOOOOOS */
    //para modulo ProvisionFondos. 
    public function esJefeAdmin(){
        $puesto = Puesto::findOrFail($this->codPuesto);
        if($puesto->nombre=='Administrador')
            return true;
        
        return false;
        
    }
    public function getPuestoActual(){
        return Puesto::findOrFail($this->codPuesto);

    }

    public function getPuesto(){
        return Puesto::findOrFail($this->codPuesto);
    }

    


    public static function getListaGerentesActivos(){
        $lista = Empleado::
            where('codPuesto','=',Puesto::getCodigo('Gerente'))
            ->where('activo','=','1')->get();
        return $lista;

    }

    public static function getListaContadoresActivos(){
        $lista = Empleado::
            where('codPuesto','=',Puesto::getCodigo('Contador'))
            ->where('activo','=','1')->get();
        return $lista;

    }

    //solo se aplica a los gerentes, retorna lista de proyectos que este gerente lidera
    public function getListaProyectos(){
        $proy = Proyecto::where('codEmpleadoDirector','=',$this->codEmpleado)->get();
        //retornamos el Collection
        return $proy;
    }

    // solo para gerente
    public function getListaSolicitudesDeGerente(){
        
        $listaSolicitudesFondos = $this->getListaSolicitudesDeGerente2()->get();
        return $listaSolicitudesFondos;

    }

    public function getListaSolicitudesDeGerente2(){
        //Construimos primero la busqueda de todos los proyectos que tenga este gerente
        $listaProyectos = $this->getListaProyectos();
        $vecProy=[];
        foreach ($listaProyectos as $itemProyecto ) {
           array_push($vecProy,$itemProyecto->codProyecto );
        }
    
        $listaSolicitudesFondos = SolicitudFondos::whereIn('codProyecto',$vecProy)
        ->orderBy('codEstadoSolicitud');

        return $listaSolicitudesFondos;

    }

    //solo para gerente
    public function getListaRendicionesGerente(){

        $listaSolicitudes = $this->getListaSolicitudesDeGerente();
        //ahora agarramos de cada solicitud, su rendicion (si la tiene)
        $listaRendiciones= new Collection();
        for ($i=0; $i < count($listaSolicitudes); $i++) { //recorremos cada solicitud
            $itemSol = $listaSolicitudes[$i];
            if(!is_null($itemSol->codSolicitud)){ 
                $itemRend = RendicionGastos::where('codSolicitud','=',$itemSol->codSolicitud)->first();
                if(!is_null($itemRend))
                    $listaRendiciones->push($itemRend);
            }
            
        }
        return $listaRendiciones;


    }

    

    public static function getEmpleadoLogeado(){
        $codUsuario = Auth::id();         
        $empleados = Empleado::where('codUsuario','=',$codUsuario)->get();

        if(is_null(Auth::id())){
            return false;
        }


        if(count($empleados)<0) //si no encontró el empleado de este user 
        {

            Debug::mensajeError('Empleado','    getEmpleadoLogeado() ');
           
            return false;
        }
        return $empleados[0]; 
    }
        

    
    public function usuario(){

        try{
        $usuario = User::findOrFail($this->codUsuario);
        
        }catch(Throwable $th){
            Debug::mensajeError('MODELO EMPLEADO', $th);
            
            return "usuario no encontrado.";


        }
        
        return $usuario;
        
    }

    
    public function getNombreCompleto(){
        return $this->apellidos.' '.$this->nombres;
    }

    public static function getEmpleadosActivos(){ //FALTA METER EN PROYECTO EL int ACTIVO
        return Empleado::where('activo','=','1')->get();
    }

    //obtiene una lista de los empleados ordenada alfabeticamente por apellidos (excepto el admin)
    //ESTA FUNCION DEBE USARSE SIEMPRE PARA MOSTRARLE A LOS USUARIOS LA LISTA DE EMPLEADOS (en un select x ejemplo) 
    public static function getListaEmpleadosPorApellido(){
        return Empleado::where('codEmpleado','!=',"0")->orderBy('apellidos')->get();


    }
    public static function getMesActual(){
        date_default_timezone_set('America/Lima');
        return (int)date('m');
    }

    
    
    



    public function reposicion(){
        $reposiciones=ReposicionGastos::where('codEmpleadoSolicitante','=',$this->codEmpleado)->get();
        return $reposiciones;
    }
}
