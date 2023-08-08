<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
</head>
<body>
    <h2>Reporte de Presupuesto de Interno </h2>
    <br>
    <br>
    <h2>Requerimiento de pago</h2>
    @php
        $total_1 = 0;
    @endphp

    @if ($data['requerimiento_saldo'])
        <table>
            <thead>
                <tr>
                    <th style="background-color: #cccccc;" width="18"><b>FECHA EMISIÓN	</b></th>
                    <th style="background-color: #cccccc;" width="30"><b>FECHA APROBACIÓN	</b></th>
                    <th style="background-color: #cccccc;text-align: center;" width="18"><b>C. ORDEN</b></th>
                    <th style="background-color: #cccccc;text-align: center;" width="18"><b>C. REQUERIMIENTO</b></th>
                    <th style="background-color: #cccccc;text-align: center;" width="18"><b>ITEM</b></th>
                    <th style="background-color: #cccccc;text-align: center;" width="18"><b>ESTADO</b></th>
                    <th style="background-color: #cccccc;text-align: center;" width="18"><b>SIMBOLO VALOR</b></th>
                    <th style="background-color: #cccccc;text-align: center;" width="18"><b>VALOR</b></th>
                    <th style="background-color: #cccccc;text-align: center;" width="18"><b>MONTO TOTAL SIMBOLO</b></th>
                    <th style="background-color: #cccccc;text-align: center;" width="18"><b>MONTO TOTAL</b></th>
                    <th style="background-color: #cccccc;text-align: center;" width="18"><b>TIPO PPTO</b></th>
                    <th style="background-color: #cccccc;text-align: center;" width="18"><b>COD PPTO</b></th>
                    <th style="background-color: #cccccc;text-align: center;" width="18"><b>NOMBRE PPTO</b></th>
                    <th style="background-color: #cccccc;text-align: center;" width="18"><b>PARTIDA</b></th>
                    <th style="background-color: #cccccc;text-align: center;" width="18"><b>DESCRIPCIÓN</b></th>
                    <th style="background-color: #cccccc;text-align: center;" width="18"><b>COD. SOFTLINK</b></th>
                    <th style="background-color: #cccccc;text-align: center;" width="18"><b>NOMBRE DESTINATARIO</b></th>


                </tr>
            </thead>
            <tbody>



                    @foreach ($data['requerimiento_saldo'] as $key_detalle => $item_detalle)
                        @php
                            $total_1 = $total_1 + ((float)$item_detalle->importe_historial);
                        @endphp
                        {{-- {{ dd($item_detalle->presupuesto_descripcion) }} --}}
                        <tr>
                            <td style="vertical-align: baseline;text-align: center;">{{ date("d/m/Y", strtotime($item_detalle->fecha_registro_req))  }}</td>
                            <td style="vertical-align: text-bottom;text-align: center;">{{ date("d/m/Y", strtotime($item_detalle->fecha_registro))}}</td>
                            <td style="vertical-align: text-bottom;text-align: center;"></td>
                            <td style="vertical-align: text-bottom;text-align: center;">{{$item_detalle->codigo_req}}</td>
                            <td style="">{{ $item_detalle->descripcion }}</td>
                            <td style="">{{ $item_detalle->estados_gasto }}</td>
                            <td style="vertical-align: text-bottom;text-align: center;">S/.</td>
                            <td style="vertical-align: text-bottom;text-align: center;">{{((float)$item_detalle->importe_historial)}}</td>
                            <td style="vertical-align: text-bottom;text-align: center;"> {{$item_detalle->monto_total_simbolo}} </td>
                            <td style="vertical-align: text-bottom;text-align: center;"> {{$item_detalle->monto_total}} </td>
                            <td style="vertical-align: text-bottom;text-align: center;">{{$item_detalle->tipo}}</td>

                            <td style="vertical-align: text-bottom;text-align: center;">{{ $item_detalle->presupuesto_codigo}}</td>
                            <td style="">{{ $item_detalle->presupuesto_descripcion}}</td>
                            <td style="vertical-align: text-bottom;text-align: center;"><p>{{$item_detalle->codigo_partida}}</p></td>
                            <td style="vertical-align: text-bottom;text-align: center;"><p>{{$item_detalle->codigo_descripcion}}</p></td>
                            <td style="vertical-align: text-bottom;text-align: center;"><p> - </p></td>
                            <td style="vertical-align: text-bottom;text-align: center;"><p>{{$item_detalle->persona}}</p></td>
                        </tr>

                    @endforeach

                {{-- <tr>
                    <td style="vertical-align: baseline;text-align: center;"></td>
                    <td style="vertical-align: text-bottom;text-align: center;"></td>
                    <td style="vertical-align: text-bottom;text-align: center;"></td>
                    <td style="vertical-align: baseline;text-align: center;"></td>
                    <td style="vertical-align: text-bottom;text-align: center;">Total : </td>
                    <td style="vertical-align: text-bottom;text-align: center;">S/.</td>
                    <td style="vertical-align: text-bottom;text-align: center;" >{{$total_1}}</td>
                </tr> --}}
            </tbody>
        </table>
    @endif

    <br>
    <br>
    @php
        $total_2 = 0;
    @endphp
    @if ($data['orden_logistico'])
   <h2>Requerimiento Logistico</h2>
    <table>
        <thead>
            <tr>
                <th style="background-color: #cccccc;" width="18"><b>FECHA EMISIÓN	</b></th>
                <th style="background-color: #cccccc;" width="18"><b>FECHA AUTORIZACIÓN	</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>C. ORDEN</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>C. REQUERIMIENTO</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>DESCRIPCIÓN</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>ESTADO</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>VALOR SIMBOLO</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>VALOR</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>MONTO TOTAL SIMBOLO</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>MONTO TOTAL</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>TIPO PPTO</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>COD PPTO</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>NOMBRE PPTO</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>PARTIDA</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>DESCRIPCIÓN</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>COD. SOFTLINK</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>PROVEEDOR</b></th>

            </tr>
        </thead>
        <tbody>

            @foreach ($data['orden_logistico'] as $item)
                    @php
                        $total_2 = $total_2 + ((float)$item->importe_historial);
                    @endphp
                <tr>
                    <td style="vertical-align: baseline;text-align: center;">{{ date("d/m/Y", strtotime($item->fecha_registro))  }}</td>
                    <td style="vertical-align: baseline;text-align: center;">{{ date("d/m/Y", strtotime($item->fecha_autorizacion))  }}</td>
                    <td style="vertical-align: text-bottom;text-align: center;">{{$item->codigo_orden}}</td>
                    <td style="vertical-align: text-bottom;text-align: center;">{{$item->codigo_req}}</td>
                    <td style="">{{$item->descripcion_adicional}}</td>
                    <td style="">{{$item->estados_gasto}}</td>
                    <td style="vertical-align: text-bottom;text-align: center;">S/.</td>
                    <td style="vertical-align: text-bottom;text-align: center;">{{((float)$item->importe_historial)}}</td>
                    <td style="vertical-align: text-bottom;text-align: center;"> {{$item->monto_total_simbolo}} </td>
                    <td style="vertical-align: text-bottom;text-align: center;"> {{$item->monto_total}} </td>
                    <td style="vertical-align: text-bottom;text-align: center;">{{$item->tipo}}</td>

                    <td style="">{{ $item->presupuesto_codigo}}</td>
                    <td style="">{{ $item->presupuesto_descripcion}}</td>
                    <td style=""><p>{{$item->codigo_partida}}</p></td>
                    <td style=""><p>{{$item->codigo_descripcion}}</p></td>

                    <td style=""><p>{{$item->codigo_softlink}}</p></td>
                    <td style=""><p>{{$item->proveedor}}</p></td>
                </tr>
            @endforeach
            {{-- <tr>
                <td style="vertical-align: baseline;text-align: center;"></td>
                <td style="vertical-align: text-bottom;text-align: center;"></td>
                <td style="vertical-align: text-bottom;text-align: center;"></td>
                <td style="vertical-align: baseline;text-align: center;"></td>
                <td style="vertical-align: text-bottom;text-align: center;">Total : </td>
                <td style="vertical-align: text-bottom;text-align: center;">S/.</td>
                <td style="vertical-align: text-bottom;text-align: center;" >{{$total_2}}</td>
            </tr> --}}
        </tbody>
    </table>
    @endif


    {{-- <br>
    <br>
    <h2>Total del Presupuesto Interno</h2>
    <table>
        <tbody>

            <tr>
                <td style="vertical-align: text-bottom;text-align: center;">Total : </td>
                <td style="vertical-align: text-bottom;text-align: center;">S/.</td>
                <td style="vertical-align: text-bottom;text-align: center;" >{{round(($total_1 + $total_2),2)}}</td>
            </tr>
        </tbody>
    </table> --}}
</body>
</html>
