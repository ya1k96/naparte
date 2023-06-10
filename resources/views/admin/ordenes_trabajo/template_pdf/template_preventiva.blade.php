<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <title>
      Masterbus | Orden de Trabajo
    </title>
    <style type="text/css">
  
        body {
            font-family: 'Open Sans', sans-serif;
            font-weight: 400;
            font-size: 10px;
            margin: 0.1cm 0.1cm;
            text-align: justify;
        }
  
        @page {
            size: "A4 portrait";
            /*A4 (210mmx297mm)*/
        }
  
        #footer {
            position: fixed;
            left: 0;
            right: 0;
            color: #000000;
            font-size: 0.9em;
  
  
        }
  
        #header {
            top: 0;
            border-bottom: 0.1pt solid #aaa;
        }
  
        #footer {
            bottom: 0;
            /* border-top: 0.1pt solid #aaa; */
        }
  
        #footer table {
            width: 100%;
            border-collapse: collapse;
            border: none;
        }
  
        #footer td {
            padding: 0;
            width: 50%;
        }
  
        .page-number {
            font-family: Arial, sans-serif;
            font-size: 1.2em;
            text-align: center;
        }

        .alinear-derecha {
            text-align: right;
        }

        .alinear-arriba {
            vertical-align: top;
        }

        .interlineado {
            line-height: 2;
        }

        .dia-estimado {
            border-radius: 5px 5px 5px;
            border: 1px solid red;
        }
  
        .page-number:before {
            float: right;
            /* content: "Pág. "counter(page); */
            text-align: left;
        }
  
        /* .barra {} */
  
        * {
            /* page-break-after: avoid;
            page-break-before: avoid;
            page-break-inside: avoid; */
        }
  
        .hr {
            order: 0;
            color: Gray;
  
        }

        .page-break {
            page-break-after: always;
        }
  
    </style>
  </head>
  
  <body marginwidth="0" marginheight="0">
    <!-- <font face="Verdana, Geneva, sans-serif"> -->
    <table width="100%" border="0">
        <!-- style="page-break-inside: avoid;" style="page-break-before: always;" -->
        <thead>
            <!-- Encabezado -->
            <tr class="top">
                <td width="100%" valign="TOP" height="20">
                    <img src="{{ base_path() }}/public/assets/img/logo_masterbus.jpg" alt="Logo Masterbus" height="70px">
                </td>
                <td width="100%" valign="TOP" height="20">
                </td>
                <td width="100%" align="RIGHT" height="20" class="alinear-derecha alinear-arriba">
                    <font size="2">
                        <b>
                            Folio N°: OT{{str_repeat('0', 6-mb_strlen((string) $id)) }}{{ $id }}{{strtoupper(mb_substr($base_operacion, 0, 3))}}
                        </b>
                    </font>
                    <br>
                    <font size="1">
                        <b align="right">
                        {{$periodo}}
                        </b>
                    </font>
                </td>
            </tr>
            <tr class="top">
                <td width="100%" valign="TOP" height="20">
                  
                </td>
                <td width="100%" align="CENTER">
                    <font size="6">
                        <b>
                        MASTERBUS
                        </b>
                    </font>
                </td>
                <td width="100%" align="RIGHT" height="20" class="alinear-derecha alinear-arriba">
                    <font size="4">
                        <b>
                        {{ $impresa }}
                        </b>
                    </font>
                </td>
            </tr>
            <tr class="top">
                <td width="100%" valign="TOP" height="20">
                </td>
                <td width="100%" align="CENTER">
                    <font size="3">
                        <b>
                        Mantenimiento {{$base_operacion}}
                        </b>
                    </font>
                </td>
                <td width="100%" valign="TOP" height="20">
                </td>
            </tr>
            <tr class="top">
                <td width="100%" valign="TOP" height="20">
                </td>
                <td width="100%" align="CENTER">
                    <font size="4">
                        <b>
                        Orden de Trabajo {{ $tipo_orden }} de: 
                            @foreach($especialidad as $k => $espec)
                                @if(count($especialidad) > 1 && $k < count($especialidad) - 1)
                                    {{ $espec->nombre.','}}
                                @elseif(count($especialidad) > 1 && $k == count($especialidad) - 1 || $k == count($especialidad) - 1)
                                        {{ $espec->nombre }}
                                @endif
                            @endforeach
                        </b>
                    </font>
                </td>
                <td width="100%" valign="TOP" height="20">
                </td>
            </tr>
            <tr>
                <td width="100%" valign="TOP" colspan="2">
                    <br />
                    <font size="4">
                        <b> EQUIPO: {{ $unidad }} </b>
                        <br />
                    </font>
                </td>
                {{-- <td width="50%" valign="TOP" colspan="2">
                    <div style="margin-left: 50px">
                        <br />
  
                    </div>
                </td> --}}
            </tr>
            <tr>
                <td width="50%" valign="TOP" colspan="1">
                    <br />
                    <font size="1">
                        <b> Generó: </b>                        
                        {{ $user }}
                        <br />
                        <b> {{$fecha}} </b>
                        <!-- <b>Revisó: </b>
                        {{ $revisado_por }} <br />
                        <br /> -->
                    </font>
                </td>
                <td width="50%" valign="TOP" colspan="2">
                    <div style="margin-left: 50px">
                        <br />
  
                        <font size="1">
                            <b>Fecha y hora de inicio de la OT: </b>
                            {{ $fecha_hora_inicio? $fecha_hora_inicio : '____________________' }}
                            <br>
                        </font>
                        <font size="1"><br><b>Fecha y hora de finalización de la OT: </b>
                          {{ $fecha_hora_fin ? $fecha_hora_fin : '____________________' }}<br>
                        </font>
                        <font size="1"><br><b>Km: </b>
                          {{ $lectura? $lectura : '____________________' }}<br>
                        </font><br />
                    </div>
                </td>
            </tr>
        </thead>
  
        <hr>
        <br />
    </table>

    <font size="1">
        <table width="115%">
            @if ($tareas)
            @php
                $contador = 0;
            @endphp
                @foreach ($tareas as $tarea)
                @php
                    $contador++;
                    if($contador % 3 == 0) {
                        echo '</table>';
                        echo '</font>';
                        echo '<div class="page-break"></div>';
                        echo '<font size="1">';
                        echo '<table width="115%">';
                    }
                @endphp
                <tr>
                    <td colspan="3" valign="TOP"><b>Tarea: {{$tarea->descripcion}} // {{$tarea->componente->nombre}} {{($tarea->componente->showPadre)? ' // ' . $tarea->componente->showPadre->nombre : ''}}</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="1" valign="TOP"></td>
                    <td colspan="2" valign="TOP" class="interlineado">
                        {{-- <br>
                        Especialidad: {{$especialidad}}
                        <br> --}}
                        <br>
                        @if($tarea->observaciones)
                        Procedimiento: {{$tarea->observaciones}}
                        <br>
                        <br>
                        @endif
                        Comentario: @if ($tarea->pivot->comentario != null)
                        {{$tarea->pivot->comentario}}
                        @else
                        {{'______________________________________________________________________________________
            ________________________________________________________________________________________________
            ________________________________________________________________________________________________
            ________________________________________________________________________________________________'}}
                        @endif
                        <br>
                        <br>
                        @if (!empty($dias))
                        @php
                            $fecha_estimada = \Carbon\Carbon::parse($tarea->pivot->fecha_estimada)->isoFormat('DD/MM/YYYY')
                        @endphp
                            @foreach ($dias as $i=>$dia)
                            @if ($i%20==0)
                                <br>
                            @endif
                                @if ($dias_fecha[$i] == $fecha_estimada)
                                    {{ '| '}}
                                <span class="dia-estimado">
                                    {{ $dia }}
                                </span>
                                @else 
                                    {{ '| '.$dia }}
                                @endif
                            @endforeach
                            <br>
                            {{-- Fecha estimada: {{$fecha_estimada}} --}}
                        @endif
                        <br>
                        <br>
                    </td>
                </tr>
                @endforeach
            @endif  

        </table>
        <br>
        <br>
        <br>
    </font>
    </td>
    </font>
    </tr>
    </tbody>
    </table>
    {{-- <table width="100%" border="0">
        <tbody>
            <tr>
                <td width="50%" valign="TOP" colspan="6">
                    <br /><br /><br /><br /><br />
                    <font size="1">
                        <b> Realizó: _____________________________________</b><br />
                    </font>
                </td>
                <td width="50%" valign="TOP" colspan="6">
                    <br /><br /><br /><br /><br />
                    <font size="1"><b>Controló: _____________________________________</b><br />
                    </font>
                </td>
            </tr>
        </tbody>
    </table> --}}
    <div id="footer">
        <table>
            <tr>
                <td width="50%" valign="TOP" colspan="6">
                    <br />
                    <font size="1">
                        <b> Realizó: _____________________________</b><br />
                    </font>
                </td>
                <td width="50%" valign="TOP" colspan="6">
                    <br />
                    <font size="1"><b>Controló: _____________________________</b><br />
                    </font>
                </td>
                <td width="53%" valign="TOP" colspan="2" class="alinear-derecha">
                    <font size="1">
                        {{-- <br />
                        <b> Generó: </b>                        
                        {{ $user }}
                        <br />
                        <b> {{$fecha}} </b>
                        <br /> --}}
                    </font>
                </td>
            </tr>
        </table>
    </div>
  </body>
  
  </html>