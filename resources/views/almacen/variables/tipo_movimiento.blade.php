@extends('themes.base')

@section('cabecera') Tipos de Operación @endsection
@include('layouts.menu_almacen')
@if(Auth::user()->tieneAccion(131))
    @section('option')
        @include('layouts.option')
    @endsection
@elseif(Auth::user()->tieneAccion(130))
    @section('option')
        @include('layouts.option_historial')
    @endsection
@endif
@section('estilos')
<link rel="stylesheet" href="{{ asset('template/adminlte2-4/plugins/select2/css/select2.css') }}">
<link rel="stylesheet" href="{{ asset('css/usuario-accesos.css') }}">
    <style>
        .invisible{
            display: none;
        }
	.d-none{
	    display: none;
    	}
    </style>
@endsection
@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('almacen.index')}}"><i class="fas fa-tachometer-alt"></i> Almacenes</a></li>
    <li>Variables</li>
    <li class="active">@yield('cabecera')</li>
  </ol>
@endsection

@section('cuerpo')
<div class="page-main" type="tipoMov">


    @if (sizeof($array_accesos_botonera)!==0 || in_array(192,$array_accesos) ||in_array(193,$array_accesos)||in_array(194,$array_accesos)||in_array(195,$array_accesos))
    <div class="row">
        <div class="col-md-6">
            <fieldset class="group-table">
                <table class="mytable table table-condensed table-bordered table-okc-view"
                id="listaTipoMov">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Cod</th>
                            <th>Tipo</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </fieldset>
        </div>
        <div class="col-md-6">
            <form id="form-tipo_movimiento" type="register" form="formulario">
                <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                <input type="text" class="oculto"  name="id_operacion">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Tipo</h5>
                        <select class="form-control activation" name="tipo" disabled="true">
                            <option value="0" selected>Elija una opción</option>
                            <option value="1">Ingreso</option>
                            <option value="2">Salida</option>
                            <option value="3">Ingreso/Salida</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <h5>Cod.Sunat</h5>
                        <input type="text" class="form-control activation"  name="cod_sunat" disabled="true">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h5>Descripción</h5>
                        <input type="text" class="form-control activation" name="descripcion" disabled="true">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                    <h5>Estado</h5>
                    <select class="form-control activation" name="estado" readonly>
                        <option value="1" selected>Activo</option>
                        <option value="2">Inactivo</option>
                    </select>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @else
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-danger pulse" role="alert">
                <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                Solicite los accesos
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')

<script src="{{ asset('template/adminlte2-4/plugins/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('template/adminlte2-4/plugins/datatables/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('template/adminlte2-4/plugins/datatables/extensions/Buttons/js/buttons.print.min.js') }}"></script>


    <script src="{{ asset('template/adminlte2-4/plugins/datatables/extensions/Buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('template/adminlte2-4/plugins/datatables/extensions/Buttons/js/buttons.bootstrap.min.js') }}"></script>
    <script src="{{ asset('template/adminlte2-4/plugins/datatables/extensions/Buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('template/adminlte2-4/plugins/datatables/extensions/Buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('template/adminlte2-4/plugins/datatables/extensions/Buttons/js/pdfmake.min.js') }}"></script>
    {{-- <script src="{{ asset('datatables/pdfmake/vfs_fonts.js') }}"></script> --}}
    <script src="{{ asset('template/adminlte2-4/plugins/datatables/extensions/Buttons/js/jszip.min.js') }}"></script>
    

    <script src="{{ asset('js/almacen/variables/tipo_movimiento.js')}}"></script>
    <script>
        var array_accesos = JSON.parse('{!!json_encode($array_accesos)!!}');
    $(document).ready(function(){
        Util.seleccionarMenu(window.location);
    });
    </script>
@endsection
{{-- ----------------------------- --}}
