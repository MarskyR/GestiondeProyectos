<table class="table table-sm">
    <thead class="thead-dark">
        <tr>
        
            <th style="text-align: center">
                Tipo Documento
            </th>
            <th>
                idDB
            </th>
            <th>
                Codigo documento
            </th>
            <th>
            Proyecto
            </th>
            <th>
            Emisor
            </th>
            <th>
                Fecha Emisión
            </th>
            <th>
                Estado
            </th>
            <th>
                Opciones    
            </th> 
        </tr>
    </thead>

    @php
        $cantidadColumnas = 8;
    @endphp

    <tbody>
        <tr>
            <td class="text-left fondoTitulos"  colspan="{{$cantidadColumnas}}">
                SOLICITUDES DE FONDOS
            </td>
        </tr>

        @foreach($listaSOF as $documento)
            <tr>
                <td>
                    SOF
                </td>
                <td>
                    {{$documento->codSolicitud}}
                </td>
                <td>
                    {{$documento->codigoCedepas}}
                </td>
                <td>
                    {{$documento->getProyecto()->nombre}}
                </td>
                <td>
                    {{$documento->getEmpleadoSolicitante()->getNombreCompleto()}}
                </td>
                <td>
                    {{$documento->getFechaHoraEmision()}}
                </td>
                <td>
                    <select class="form-control" name="sof_codEstado" id="sof_codEstado" onchange="cambiarEstado(this.value,'SOF',{{$documento->getID()}})">
                        @foreach ($sof_estados as $estado)
                            <option value="{{$estado->getID()}}"
                                @if($estado->getID()==$documento->getEstado()->getID())
                                    selected
                                @endif
                                >
                                {{$estado->nombre}}
                            </option>
                        @endforeach
                    </select>
                    
                </td>

                <td>
                    {{-- OPCIONES SOLICITUD--}}
                    @if($documento->verificarEstado('Aprobada')) {{-- Si está aprobada (pa abonar) --}}   
                        <a class='btn btn-warning btn-sm' href="{{route('SolicitudFondos.Administracion.verAbonar',$documento->codSolicitud)}}" 
                            title="Abonar Solicitud">
                            <i class="fas fa-hand-holding-usd"></i>
                        </a>
                    @else{{-- si está rendida (pa verla nomas ) --}}
                    
                        <a href="{{route('SolicitudFondos.Administracion.verAbonar',$documento->codSolicitud)}}" class='btn btn-info btn-sm' title="Ver Solicitud">
                            <i class="fas fa-eye"></i>
                        </a>
                        
                        
                    @endif

                </td>
            </tr>
        @endforeach

        <tr>
            <td class="text-left fondoTitulos" colspan="{{$cantidadColumnas}}">
                RENDICIONES DE GASTOS
            </td>
        </tr>

        @foreach($listaREN as $documento)
            <tr>
                <td>
                    REN
                </td>
                <td>
                    {{$documento->codRendicionGastos}}
                </td>
                <td>
                    {{$documento->codigoCedepas}}
                </td>
                <td>
                    {{$documento->getProyecto()->nombre}}
                </td>
                <td>
                    {{$documento->getEmpleadoSolicitante()->getNombreCompleto()}}
                </td>
                <td>
                    {{$documento->formatoFechaHoraRendicion()}}
                </td>
                <td>
                    <select class="form-control" name="ren_codEstado" id="ren_codEstado" onchange="cambiarEstado(this.value,'REN',{{$documento->getID()}})">
                        @foreach ($ren_estados as $estado)
                            <option value="{{$estado->getID()}}"
                                @if($estado->getID()==$documento->getEstado()->getID())
                                    selected
                                @endif
                                >
                                {{$estado->nombre}}
                            </option>
                        @endforeach
                    </select>
                    
                </td>

                <td >        
                
                    <a href="{{route('SolicitudFondos.Empleado.Ver',$documento->getSolicitud()->codSolicitud)}}" class='btn btn-info btn-sm' title="Ver Solicitud">
                    S
                    </a>

                    @if(!$documento->verificarEstado('Contabilizada') )
                        {{-- Es en estos estados que puede observar --}}  
                        <a href="{{route('RendicionGastos.Administracion.Ver',$documento->codRendicionGastos)}}" class='btn btn-warning btn-sm' title="Revisar Rendición">
                            <i class="fas fa-eye">R</i>
                        </a>
                    
                    @else 

                        
                        <a href="{{route('RendicionGastos.Administracion.Ver',$documento->codRendicionGastos)}}" class='btn btn-info btn-sm' title="Ver Rendición">
                            R
                        </a>
                    @endif

                </td>  
            </tr>
        @endforeach

        <tr>
            <td class="text-left fondoTitulos"  colspan="{{$cantidadColumnas}}">
                REPOSICIONES DE GASTOS
            </td>
        </tr>

        @foreach($listaREP as $documento)
            <tr>
                <td>
                    REP
                </td>
                <td>
                    {{$documento->codReposicionGastos}}
                </td>
                <td>
                    {{$documento->codigoCedepas}}
                </td>
                <td>
                    {{$documento->getProyecto()->nombre}}
                </td>
                <td>
                    {{$documento->getEmpleadoSolicitante()->getNombreCompleto()}}
                </td>
                <td>
                    {{$documento->formatoFechaHoraEmision()}}
                </td>
                <td>
                    <select class="form-control" name="rep_codEstado" id="rep_codEstado" onchange="cambiarEstado(this.value,'REP',{{$documento->getID()}})">
                        @foreach ($rep_estados as $estado)
                            <option value="{{$estado->getID()}}"
                                @if($estado->getID()==$documento->getEstado()->getID())
                                    selected
                                @endif
                                >
                                {{$estado->nombre}}
                            </option>
                        @endforeach
                    </select>
                    
                </td>
                <td >
                    @if($documento->codEstadoReposicion==2)
                        <a href="{{route('ReposicionGastos.Administracion.ver',$documento->codReposicionGastos)}}" class="btn btn-warning btn-sm" title="Abonar Reposición">
                            <i class="fas fa-thumbs-up"></i>
                        </a>
                    @else
                        <a href="{{route('ReposicionGastos.Administracion.ver',$documento->codReposicionGastos)}}" class="btn btn-info btn-sm" title="Ver Reposición">
                            <i class="fas fa-eye"></i>
                        </a>
                    @endif
                    
                </td>
            </tr>
        @endforeach

        <tr>
            <td class="text-left fondoTitulos"  colspan="{{$cantidadColumnas}}">
                REQUERIMIENTOS DE BIENES Y SERVICIOS
            </td>
        </tr>

        @foreach($listaREQ as $documento)
            <tr>
                <td>
                    REQ
                </td>
                <td>
                    {{$documento->codRequerimiento}}
                </td>
                <td>
                    {{$documento->codigoCedepas}}
                </td>
                <td>
                    {{$documento->getProyecto()->nombre}}
                </td>
                <td>
                    {{$documento->getEmpleadoSolicitante()->getNombreCompleto()}}
                </td>
                <td>
                    {{$documento->formatoFechaHoraEmision()}}
                </td>
                <td>
                    <select class="form-control" name="req_codEstado" id="req_codEstado" onchange="cambiarEstado(this.value,'REQ',{{$documento->getID()}})">
                        @foreach ($req_estados as $estado)
                            <option value="{{$estado->getID()}}"
                                @if($estado->getID()==$documento->getEstado()->getID())
                                    selected
                                @endif
                                >
                                {{$estado->nombre}}
                            </option>
                        @endforeach
                    </select>
                    
                </td>
                <td>       
                    @if($documento->listaParaAtender() )
                    <a href="{{route('RequerimientoBS.Administrador.VerAtender',$documento->codRequerimiento)}}" 
                        class="btn btn-warning btn-sm" title="Atender Requerimiento">
                        <i class="fas fa-store"></i>
                    </a>
                    @else
                    <a href="{{route('RequerimientoBS.Administrador.VerAtender',$documento->codRequerimiento)}}"
                        class="btn btn-info btn-sm" title="Ver Requerimiento">
                        <i class="fas fa-eye"></i>
                    </a>
                    @endif
    
                </td>
            </tr>
        @endforeach

        

    </tbody>
</table>