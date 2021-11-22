@include('RequerimientoBS.Plantillas.SubPlantillaVerReqSuperior')

<div class="row" id="" name="">                       
    <div class="col" style="">
        @include('RequerimientoBS.Plantillas.Desplegables.DescargarArchivosEmp')

    </div> 
    @if($requerimiento->tieneArchivosAdmin() )
        <div class="col">
                @include('RequerimientoBS.Plantillas.Desplegables.DescargarArchivosAdm')
        </div>
    @endif
    <div class="col">
        <a  href="{{route('RequerimientoBS.exportarPDF',$requerimiento->codRequerimiento)}}" 
            class="btn btn-primary float-left" title="Descargar PDF" style="margin-right:6px;">
            Descargar en PDF
            <i class="fas fa-file-download"></i>
            </a>

            <a target="pdf_reposicion_{{$requerimiento->codRequerimiento}}" href="{{route('RequerimientoBS.verPDF',$requerimiento->codRequerimiento)}}" 
            class="btn btn-primary float-left" title="Ver PDF">
            Ver PDF
            <i class="fas fa-file-pdf"></i>
            </a>
    </div>


</div>
        
        
 