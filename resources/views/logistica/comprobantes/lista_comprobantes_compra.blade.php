@extends('themes.base')
@include('layouts.menu_tesoreria')

@section('cabecera')
    Reporte de Comprobantes de Compra
@endsection
@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('tesoreria.index')}}"><i class="fas fa-tachometer-alt"></i> Tesorería</a></li>
    <li>Comprobantes</li>
    <li class="active">@yield('cabecera')</li>
</ol>
@endsection
@section('cuerpo')
<div class="page-main" type="lista_comprobantes_compra">

    <div class="box box-solid">
        <div class="box-body">
            <div class="col-md-12" style="padding-top:10px;padding-bottom:10px;">
                <a class="btn btn-success" href="lista-comprobantes-pago-export-excel"><i class="fas fa-file-excel"></i> Exportar Excel</a>
                @if (Auth::user()->id_usuario == 3)
                <button id="btn_actualizar_sedes" class="btn btn-default" onClick="actualizarSedesFaltantes();">Actualizar Sedes</button>
                <button id="btn_actualizar_proveedores" class="btn btn-default" onClick="actualizarProveedores();">Actualizar Proveedores</button>
                <button id="btn_actualizar_items" class="btn btn-default" onClick="migrarItemsComprobantesSoftlink();">Actualizar Items</button>
                <button id="btn_migrar_comprobantes" class="btn btn-success" onClick="migrarComprobantesSoftlink();">Migrar a Softlink</button>


                @endif

                <div class="row">
                    <div class="col-sm-12">
                        <!-- <caption>Requerimientos: Registrados | Aprobados</caption> -->
                        <table class="mytable table table-hover table-condensed table-bordered table-okc-view"
                            id="listaComprobantesCompra" width="100%">
                            <thead>
                                <tr>
                                    <th hidden>#</th>
                                    <th>Empresa</th>
                                    <th>Tipo Doc.</th>
                                    <th>Serie</th>
                                    <th>Número</th>
                                    <th>Cód. Softlink</th>
                                    <th>RUC</th>
                                    <th>Proveedor</th>
                                    <th>Fecha Emisión</th>
                                    <th>Condición</th>
                                    <th>Fecha Vencimiento</th>
                                    <th>Mnd</th>
                                    <th>Total a Pagar</th>
                                    {{-- <th>Estado</th> --}}
                                    <th>Acción</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@include('almacen.documentos.doc_com_ver')
@endsection

@section('scripts')
    <script src="{{ asset('template/adminlte2-4/plugins/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('template/adminlte2-4/plugins/datatables/js/dataTables.bootstrap.min.js') }}"></script>
    <!-- <script src="{{ asset('template/adminlte2-4/plugins/datatables/extensions/Buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('template/adminlte2-4/plugins/datatables/extensions/Buttons/js/buttons.bootstrap.min.js') }}"></script>
    <script src="{{ asset('template/adminlte2-4/plugins/datatables/extensions/Buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('template/adminlte2-4/plugins/datatables/extensions/Buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('template/adminlte2-4/plugins/datatables/extensions/Buttons/js/pdfmake.min.js') }}"></script>
    <script src="{{ asset('template/adminlte2-4/plugins/datatables/extensions/Buttons/js/vfs_fonts.js') }}"></script>
    <script src="{{ asset('template/adminlte2-4/plugins/datatables/extensions/Buttons/js/jszip.min.js') }}"></script> -->
    

    <script src="{{('/js/logistica/comprobantes/lista_comprobantes_compra.js')}}"></script>
    <!-- <script src="{{('/js/logistica/comprobantes/doc_compra.js')}}"></script> -->
    <script src="{{ asset('js/almacen/documentos/doc_com_ver.js')}}"></script>

    <script>
        $(document).ready(function() {
            Util.seleccionarMenu(window.location);
            // $.fn.dataTable.Buttons.defaults.dom.button.className = 'btn';
            // iniciar('{{Auth::user()->tieneAccion(83)}}');
            listar_doc_compra();
        });
    </script>

@endsection
