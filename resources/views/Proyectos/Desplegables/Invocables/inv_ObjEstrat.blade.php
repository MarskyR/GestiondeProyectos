@php
    if($proyecto->sePuedeEditar()){
        $readonly="";
        $disabled = "";
        $returnFalse = '';
    }
    else{ 
        $readonly="readonly";
        $disabled = 'disabled';
        $returnFalse = 'return false;';
    }   
@endphp

<table id="" class="table-sm table table-striped table-bordered table-condensed table-hover fontSize9 m-2"  
    style='background-color:#FFFFFF; width:95%;'> 
    <thead class="thead-default" style="background-color:#3c8dbc;color: #fff;">
        <th  class="text-center" width="2%"> Item</th>
        <th  class="text-center" width="5%"> Nombre</th>
        <th  class="text-center" width="50%">Descripción</th>                                        
        <th  class="text-center" width="20%">0-100%</th>                                        
    </thead>
    <tbody>
        @foreach($listaPorcentajes as $itemPorcentajeObj)
            <tr class="selected">
                <td>
                    {{-- {{$itemPorcentajeObj->getObjetivoEstrategico()->item}} --}}
                    
                </td>
                <td>
                    {{-- {{$itemPorcentajeObj->getObjetivoEstrategico()->nombre}} --}}
                    
                </td>
                
                <td  style="text-align:jusitfy;" class="fontSize8">               
                    {{-- {{$itemPorcentajeObj->getObjetivoEstrategico()->descripcion}} --}}
                </td>       
                <td  style="text-align:right;"> 
                    
{{--                     <input class="form-control" step="1"  type="number" id="porcentaje{{$itemPorcentajeObj->codRelacion}}" {{$readonly}} 
                        name="porcentaje{{$itemPorcentajeObj->codRelacion}}" onchange="actualizarPorcentajeTotalEstrategicos()"
                    style="width: 80px; text-align:center" value="{{$itemPorcentajeObj->porcentajeDeAporte}}" 
                    min="0" max="100"> --}}
                    
                </td>               
                
                        
            </tr>                
        @endforeach    
        <tr style="background-color: rgb(170, 170, 170)">
            <td></td>
            <td></td>
            <td class="fontSize12 text-right">
                <b >
                    Total:
                </b>
                
            </td>
            <td class="text-center fontSize12" >
                {{-- Aqui se muestra el total --}}
                <b id="totalPorcentajeEstrategicos">
                    {{$proyecto->getTotalAportesEstrategicos()}}
                </b>
                

            </td>
        </tr>
    </tbody>
</table>
    


 