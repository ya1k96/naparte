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
            /* border-bottom: 0.1pt solid #aaa; */
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
            line-height: 3;
        }
  
        .page-number:before {
            float: right;
            /* content: "Pág. "counter(page); */
            text-align: left;
        }
  
        /* .barra {} */
  
        * {
            page-break-after: avoid;
            page-break-before: avoid;
            page-break-inside: avoid;
        }
  
        .hr {
            order: 0;
            color: Gray;
  
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
                        <b align="right">
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
                        Orden de Trabajo {{ $tipo_orden }}
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
                {{-- <td width="50%" valign="TOP" colspan="1">
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

    
    <br/>
    <table width="115%">
            <tr>
                <td colspan="3" valign="TOP">
                        <font size="3">
                        <b>Tarea: {{$tarea_a_realizar}}</b>
                    </font>
                </td>
            </tr>
            <tr>
                <font size="1">
                    <td colspan="3">
                        @if (!empty($dias))
                            @foreach ($dias as $i=>$dia)
                            @if ($i%10==0)
                                <br>
                            @endif
                                {{ '| '.$dia }}
                            @endforeach
                            <br>
                        @endif
                    </td>
                </font>
            </tr>
            <tr>
                <td colspan="1" valign="TOP"></td>
                <td colspan="3" valign="TOP" class="interlineado">
                    <font size="3">
                        <br>
                        Trabajo realizado: 
                    </font>
                        {{$procedimiento_realizado}}
                        <br>
                        <br>
                    </td>
            </tr>
        </table>
        <br>
        <br>
        <br>

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