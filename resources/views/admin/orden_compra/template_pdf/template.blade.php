<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <title>
      Masterbus | Orden de Compra
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

        .borde {
            border: 1px solid #000;
        }

        .borde-right {
            border-right: 1px solid #000;
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
                    <img src="{{ $empresa_img }}" alt="Logo Masterbus" height="70px">
                </td>
                <td width="100%" valign="TOP" height="20">
                </td>
                <td width="100%" align="RIGHT" height="20" class="alinear-derecha alinear-arriba">
                    <font size="2">
                        <b align="right">
                        {{ $id }}
                        </b>
                    </font>
                    <br>
                    <font size="1">
                        <b align="right">
                        Fecha:
                        </b>
                        {{$fecha}}
                    </font>
                </td>
            </tr>
            <tr class="top">
                <td width="100%" valign="TOP" height="20">

                </td>
                <td width="100%" align="CENTER">
                    <font size="6">
                        <b>
                        {{$empresa_nombre}}
                        </b>
                    </font>
                </td>
                <td width="100%" align="RIGHT" height="20" class="alinear-derecha alinear-arriba">
                    <font size="4">
                    </font>
                </td>
            </tr>
            <tr class="top">
                <td width="100%" valign="TOP" height="20">
                </td>
                <td width="100%" align="CENTER">
                    <font size="3">

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
                        Orden de Compra / Requisición
                        </b>
                    </font>
                </td>
                <td width="100%" valign="TOP" height="20">
                </td>
            </tr>
        </thead>

        <hr>
        <br />
        <br>
    </table>

    <font size="1">
        <table width="100%">
            <tr>
                <td valign="TOP" class="alinear-derecha"><b>Fecha de entrega:</b> {{$fecha_entrega}}
                </td>
            </tr>
            <hr>
        </table>
        <table width="115%">
            <tr>
                <td colspan="3" valign="TOP"><b>Proveedor:</b> {{$nombre_proveedor}}
                </td>
                <td colspan="3" valign="TOP"><b>RFC:</b> {{$cuit_proveedor}}
                </td>
            </tr>
            <tr>
                <td colspan="3" valign="TOP">
                </td>
                <td colspan="3" valign="TOP"><b>Entregar en:</b> {{$entregar_en}}
                </td>
            </tr>
            <hr>
        </table>
        <table width="100%">
            <tr>
                <td>
                    <table width="100%" cellpadding="10" cellspacing="2">
                        <thead>
                            <tr align="CENTER" valign="middle">
                                <td>Part</td>
                                <td>N° de parte</td>
                                <td>Descripcion</td>
                                <td>Presentacion</td>
                                <td>Cant.</td>
                                <td>Costo Unitario</td>
                                <td>Sub Total</td>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $subtotal = 0;
                            @endphp
                            @foreach ($detalles as $i=>$detalle)
                                @php
                                    $subtotal += $detalle->costo * $detalle->cantidad;
                                @endphp
                                <tr align="center" valign="middle">
                                    <td>{{$i+1}}</td>
                                    <td>{{$detalle->pieza->nro_pieza}}</td>
                                    <td>{{$detalle->pieza->descripcion}}</td>
                                    <td>{{$detalle->pieza->unidadMedida->nombre}}</td>
                                    <td>{{$detalle->cantidad}}</td>
                                    <td>{{$detalle->costo}}</td>
                                    <td>$ {{$subtotal}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
        <table width="100%">
            <tr>
                <td width="90%" valign="TOP"><b>Observacion:</b> {{$observacion}}
                </td>
                <td>
                    <table table width="100%">
                        <tr>
                            <td valign="TOP" class="alinear-derecha">
                                <hr>
                                <b>$ {{$subtotal}}</b><br>
                                {{-- <b>$ 0</b><br> --}}
                                {{-- <b>$ {{$subtotal}}</b> --}}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </font>
    </td>
    </font>
    </tr>
    </tbody>
    </table>
    <div id="footer">
        <table>
            <tr>
                <td width="100%" valign="TOP">
                    <font size="1">
                        <b> Referencia: </b>
                    </font>
                </td>
            </tr>
        </table>
        <br/>
        <table>
            <tr width="100%">
                <td width="25%" valign="TOP" colspan="6">
                    <font size="1">
                        <b> Solicitó: ____________________</b>
                    </font>
                </td>
                <td width="25%" valign="TOP" colspan="6">
                    <font size="1"><b>Elaboró: ____________________</b>
                    </font>
                </td>
                <td width="25%" valign="TOP" colspan="6">
                    <font size="1"><b>Autorizó: ____________________</b>
                    </font>
                </td>
                <td width="25%" valign="TOP" colspan="6">
                    <font size="1"><b>Recibió: ____________________</b>
                    </font>
                </td>
            </tr>
        </table>
    </div>
  </body>

  </html>
