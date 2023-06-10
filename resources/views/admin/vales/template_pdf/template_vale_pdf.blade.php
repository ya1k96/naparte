<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <title>
      Masterbus | Vale
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
            bottom: 42px;
        }
  
        #header {
            top: 0;
            border-bottom: 0.1pt solid #aaa;
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
                            (clave ISO)
                        </b>
                    </font>
                    <br>
                    <font size="1">
                        <b align="right">
                            (revisión ISO)                        
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
                <td width="100%" align="CENTER" style="padding-bottom: 50px;">
                    <font size="4">
                        <b>
                        Vale de Materiales
                        </b>
                    </font>
                </td>
                <td width="100%" valign="TOP" height="20">
                </td>
            </tr>
            <tr>
                <td width="30%" valign="TOP" style="text-align: left;">
                    <font size="1">
                        <b> Folio vale: VAL{{str_repeat('0', 6-mb_strlen((string) $id)) }}{{ $id }}{{strtoupper(mb_substr($base_operacion, 0, 3))}}</b>                        
                    </font>
                </td>
                <td width="30%" valign="TOP" style="text-align: center;">
                    <font size="1">
                        <b> Fecha: {{ $fecha_vale }}</b>                        
                    </font>
                </td>
                <td width="30%" valign="TOP" style="text-align: right;">
                    <font size="1">
                        <b> Folio OT: OT{{str_repeat('0', 6-mb_strlen((string) $id)) }}{{ $orden_id }}{{strtoupper(mb_substr($base_operacion, 0, 3))}}</b>                        
                    </font>
                </td>
            </tr>
        </thead>
  
        <hr>
        <br />
    </table>
    <br />
    <br />

    <table>
        <thead>
            <tr>
                <th style="background-color:rgb(201, 201, 201); border: solid; width: 100%;"><font size="3">EQUIPO: {{ $unidad }} </font></th>
            </tr>
        </thead>
    </table>

    <table>
        <thead>
            <tr>
                <th style="padding-bottom: 20px; padding-top: 20px;"><font size="2">RECURSOS SELECCIONADOS</font></th>
                <th></th>
                <th></th>
            </tr>
        </thead>
    </table>

    <font size="1">
        <table width="100%">
            <thead>
                <tr>
                    <th>Material</th>
                    <th>No. Parte</th>
                    <th>Cantidad</th>
                    <th>Unidad</th>
                    <th>Localización</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($vales_detalles as $vale)
                    <tr>
                        <td style="text-align: center;">{{$vale['material']}}</td>
                        <td style="text-align: center;">{{$vale['nro_parte']}}</td>
                        <td style="text-align: center;">{{$vale['cantidad']}}</td>
                        <td style="text-align: center;">{{$vale['unidad']}}</td>
                        <td style="text-align: center;">{{$vale['localizacion']}}</td>
                    </tr>
                @endforeach
            </tbody>
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
    <div id="footer">
        <table>
            <tbody>
                <tr>
                    <td style="width: 20%; height: 24px">Solicitado por:</td>
                    <td></td>
                    <td style="width: 20%; height: 24px">Autorizado por:</td>
                    <td></td>
                </tr>
                <tr>
                    <td style="width: 20%; height: 24px">Nombres y apellidos:</td>
                    <td>____________________</td>
                    <td style="width: 20%; height: 24px">Nombres y apellidos:</td>
                    <td>____________________</td>
                </tr>
                <tr>
                    <td style="width: 20%; height: 24px">Firma:</td>
                    <td>____________________</td>
                    <td style="width: 20%; height: 24px">Firma:</td>
                    <td>____________________</td>
                </tr>
                <tr>
                    <td>Fecha: {{$fecha}}</td>
                </tr>
            </tbody>
        </table>
    </div>
  </body>
  
  </html>