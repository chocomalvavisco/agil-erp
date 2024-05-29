<?php

namespace App\Http\Controllers\Logistica;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AlmacenController;
use App\Http\Controllers\Controller;
use App\Models\Administracion\Periodo;
use App\Models\Almacen\DetalleRequerimiento;
use App\Models\Almacen\Requerimiento;
use App\Models\Almacen\Reserva;
use App\Models\Almacen\UnidadMedida;
use App\models\Configuracion\AccesosUsuarios;
use App\Models\Configuracion\LogActividad;
use App\Models\Configuracion\Moneda;
use App\Models\Contabilidad\Banco;
use App\Models\Contabilidad\ContactoContribuyente;
use App\Models\Contabilidad\Contribuyente;
use App\Models\Contabilidad\CuentaContribuyente;
use App\Models\Contabilidad\TipoCuenta;
use App\Models\Logistica\CondicionSoftlink;
use App\Models\Logistica\Orden;
use App\Models\Logistica\OrdenCompraDetalle;
use App\Models\Logistica\Proveedor;
use App\Models\mgcp\CuadroCosto\Proveedor as CuadroCostoProveedor;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Exception;
date_default_timezone_set('America/Lima');

class OrdenMultipleController extends Controller
{
 
    
    function view_orden_multiple()
    {
        $tp_documento = $this->select_documento();
        $tp_moneda = $this->select_moneda();
        $periodos = Periodo::mostrar();
        $sedes = $this->select_empresa_sede();
        $proveedores = $this->listar_proveedores();
        $rubros = $this->select_mostrar_rubos();

        $empresas = $this->select_mostrar_empresas();
        $bancos = Banco::mostrar();
        $tipo_cuenta = TipoCuenta::mostrar();
        $sis_identidad = $this->select_sis_identidad();
        $ubigeos = $this->select_ubigeos();
        $trabajadores = $this->select_trabajadores();



        $condiciones = $this->select_condiciones();
        $condiciones_softlink = CondicionSoftlink::mostrar();
 
        $tp_doc = $this->select_tp_doc();
        $clasificaciones = (new AlmacenController)->mostrar_clasificaciones_cbo();
        $subcategorias = (new AlmacenController)->mostrar_subcategorias_cbo();
        $categorias = (new AlmacenController)->mostrar_categorias_cbo();
        $unidades = (new AlmacenController)->mostrar_unidades_cbo();
        $unidades_medida = UnidadMedida::mostrar();
        $monedas = Moneda::mostrar();
        // $array_accesos_botonera = array();
        // $accesos_botonera = AccesosUsuarios::where('accesos_usuarios.estado', '=', 1)
        //     ->select('accesos.*')
        //     ->join('configuracion.accesos', 'accesos.id_acceso', '=', 'accesos_usuarios.id_acceso')
        //     ->where('accesos_usuarios.id_usuario', Auth::user()->id_usuario)
        //     ->where('accesos_usuarios.id_modulo', 91)
        //     ->where('accesos_usuarios.id_padre', 89)
        //     ->get();
        // foreach ($accesos_botonera as $key => $value) {
        //     $value->accesos;
        //     array_push($array_accesos_botonera, $value->accesos->accesos_grupo);
        // }
        $modulo = 'logistica';
        // $array_accesos = [];
        // $accesos_usuario = AccesosUsuarios::where('estado', 1)->where('id_usuario', Auth::user()->id_usuario)->get();
        // foreach ($accesos_usuario as $key => $value) {
        //     array_push($array_accesos, $value->id_acceso);
        // }

        return view('logistica.gestion_logistica.compras.ordenes.elaborar.orden_multiple', compact('empresas', 'rubros', 'bancos', 'tipo_cuenta', 'sedes','proveedores', 'sis_identidad', 'tp_documento', 'tp_moneda', 'tp_doc', 'condiciones', 'condiciones_softlink','ubigeos','trabajadores', 'clasificaciones', 'subcategorias', 'categorias', 'unidades', 'unidades_medida', 'monedas', 'modulo',  'periodos'));
    }


    public function obtenerDataProveedor($idProveedor)
    {
        $proveedor = Proveedor::with(['contribuyente.tipoDocumentoIdentidad', 'estadoProveedor','contactoContribuyente' ,'cuentaContribuyente' => function ($q) {
                $q->where('estado', '=', 1);
            }, 'cuentaContribuyente.banco.contribuyente','cuentaContribuyente.tipoCuenta','cuentaContribuyente.moneda'])
                ->whereHas('contribuyente', function ($q) {
                    $q->where('estado', '=', 1);
                })->where([['id_proveedor','=',$idProveedor],['log_prove.estado', '=', 1]])->get()->first();

        return $proveedor;
    }

    
    public function listar_proveedores(){
        $proveedores = Proveedor::with(['contribuyente.tipoDocumentoIdentidad', 'estadoProveedor', 'cuentaContribuyente' => function ($q) {
            $q->where('estado', '=', 1);
        }])->whereHas('contribuyente', function ($q) {
                $q->where('estado', '=', 1);
        })->where('log_prove.estado', '=', 1)->get();
        
        return $proveedores;
    }

    function lista_contactos_proveedor($id_proveedor)
    {

        $data = DB::table('logistica.log_prove')
            ->select(
                'adm_ctb_contac.id_datos_contacto as id_contacto',
                'adm_ctb_contac.nombre as nombre_contacto',
                'adm_ctb_contac.cargo as cargo_contacto',
                'adm_ctb_contac.email as email_contacto',
                'adm_ctb_contac.telefono as telefono_contacto',
                'adm_ctb_contac.direccion as direccion_contacto',
                'adm_ctb_contac.ubigeo as ubigeo_contacto'
            )
            // ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->join('contabilidad.adm_ctb_contac', 'adm_ctb_contac.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->where('log_prove.id_proveedor', $id_proveedor)
            ->orderby('adm_ctb_contac.nombre', 'asc')
            ->get();


        return response()->json(['data' => $data]);
    }

    public function select_documento()
    {
        $data = DB::table('administracion.adm_tp_docum')
            ->select('adm_tp_docum.id_tp_documento', 'adm_tp_docum.descripcion', 'adm_tp_docum.abreviatura')
            ->where([
                ['adm_tp_docum.estado', '=', 1],
                ['adm_tp_docum.uso_logistica', true]
            ])
            ->orderBy('adm_tp_docum.id_tp_documento', 'asc')
            ->get();
        return $data;
    }

    public function select_moneda()
    {
        $data = DB::table('configuracion.sis_moneda')
            ->select('sis_moneda.id_moneda', 'sis_moneda.descripcion', 'sis_moneda.simbolo')
            ->where([
                ['sis_moneda.estado', '=', 1]
            ])
            ->orderBy('sis_moneda.id_moneda', 'asc')
            ->get();
        return $data;
    }

    public function select_sedes()
    {
        $data = DB::table('administracion.sis_sede')
            ->select(
                'sis_sede.*'
            )
            ->orderBy('sis_sede.id_empresa', 'asc')
            ->get();
        return $data;
    }

    public function select_empresa_sede()
    {
        $data = DB::table('administracion.sis_sede')
            ->select(
                'sis_sede.*',
                'adm_contri.razon_social as razon_social_empresa',
                'ubi_dis.descripcion as ubigeo_descripcion'
            )
            ->leftJoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
            ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->leftJoin('configuracion.ubi_dis', 'ubi_dis.id_dis', '=', 'sis_sede.id_ubigeo')
            ->where('sis_sede.estado', '=', '1')
            ->orderBy('sis_sede.id_empresa', 'asc')
            ->get();
        return $data;
    }

    public function select_tp_doc()
    {
        $data = DB::table('contabilidad.cont_tp_doc')
            ->select('cont_tp_doc.id_tp_doc', 'cont_tp_doc.cod_sunat', 'cont_tp_doc.descripcion')
            ->where([['cont_tp_doc.estado', '=', 1]])
            ->orderBy('cont_tp_doc.id_tp_doc')
            ->get();
        return $data;
    }

    public function select_condiciones()
    {
        $data = DB::table('logistica.log_cdn_pago')
            ->select('log_cdn_pago.id_condicion_pago', 'log_cdn_pago.descripcion')
            ->where('log_cdn_pago.estado', 1)
            ->orderBy('log_cdn_pago.descripcion')
            ->get();
        return $data;
    }

    public function select_sis_identidad()
    {
        $data = DB::table('contabilidad.sis_identi')
            ->select('sis_identi.id_doc_identidad', 'sis_identi.descripcion')
            ->where('sis_identi.estado', '=', 1)
            ->orderBy('sis_identi.descripcion', 'asc')->get();
        return $data;
    }

    
    public function select_ubigeos()
    {
        $data = DB::table('configuracion.ubi_dis')
            ->select('ubi_dis.*', 'ubi_prov.descripcion as provincia', 'ubi_dpto.descripcion as departamento')
            ->join('configuracion.ubi_prov', 'ubi_prov.id_prov', '=', 'ubi_dis.id_prov')
            ->join('configuracion.ubi_dpto', 'ubi_dpto.id_dpto', '=', 'ubi_prov.id_dpto')
            ->get();
      
        return $data;
    }

    public function select_trabajadores()
    {
        $data = DB::table('rrhh.rrhh_trab')
                ->select('rrhh_trab.*', 'rrhh_perso.nro_documento',
                DB::raw("concat(rrhh_perso.nombres, ' ' ,rrhh_perso.apellido_paterno, ' ' ,rrhh_perso.apellido_materno) AS nombre_trabajador"))
                ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
                ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                ->where([['rrhh_trab.estado', '=', 1]])
                ->orderBy('nombre_trabajador')
                ->get();
         
        return $data;
    }

    public function select_mostrar_empresas()
    {
        $data = DB::table('administracion.adm_empresa')
            ->select('adm_empresa.id_empresa', 'adm_empresa.codigo', 'adm_empresa.logo_empresa', 'adm_contri.nro_documento', 'adm_contri.razon_social')
            ->join('contabilidad.adm_contri', 'adm_empresa.id_contribuyente', '=', 'adm_contri.id_contribuyente')
            ->where('adm_empresa.estado', '=', 1)
            ->orderBy('adm_contri.razon_social', 'asc')
            ->get();
        return $data;
    }
    public function select_mostrar_rubos()

    {
        $data = DB::table('contabilidad.adm_rubro')
            ->where('adm_rubro.estado', '=', 1)
            ->orderBy('adm_rubro.descripcion', 'asc')
            ->get();
        return $data;
    }


    public function ObtenerAtencionItemRequerimiento($idRequerimiento){
        $isSuccess=true;
        $listaAtendidoOrden=[];
        $estadoItems=[];

        $requerimiento = Requerimiento::with('periodo','moneda','empresa.contribuyente','sede')->find($idRequerimiento);
        if(in_array($requerimiento->estado,[2,15,27])){
            $detalleRequerimiento = DetalleRequerimiento::with('producto')->where([['id_requerimiento',$idRequerimiento],['estado','!=',7]])->get();

            //añadir proveedor equivalente en agile
            foreach ($detalleRequerimiento as $keyDetReq => $DetReqValue) {
                $detalleRequerimiento[$keyDetReq]['proveedor']=$this->obtenerProveedorEquivalente($DetReqValue->proveedor_seleccionado_id, $DetReqValue->proveedor_seleccionado);
            }

            foreach ($detalleRequerimiento as $detReq) {

                    $estadoItems[]=[
                        'id_requerimiento'=>intval($detReq->id_requerimiento),
                        'id_detalle_requerimiento'=>intval($detReq->id_detalle_requerimiento),
                        'id_tipo_item'=>intval($detReq->id_tipo_item),
                        'cantidad_solicitada'=>intval($detReq->cantidad),
                        'cantidad_atendida_orden'=>0,
                        'cantidad_atendida_almacen'=>0,
                        'tiene_atencion_total'=>false
                    ];

                $detalleOrden = OrdenCompraDetalle::where([['id_detalle_requerimiento',$detReq->id_detalle_requerimiento],['estado','!=',7]])->get();
                foreach ($detalleOrden as $keyDetOrd => $detOrd) {
                    foreach ($estadoItems as $keyEstItem => $estItem) {
                        if($estItem['id_detalle_requerimiento'] == $detOrd->id_detalle_requerimiento){
                            $estadoItems[$keyEstItem]['cantidad_atendida_orden'] += intval($detOrd->cantidad);
                            if($estadoItems[$keyEstItem]['cantidad_atendida_orden']==$estItem['cantidad_solicitada']){
                                $estadoItems[$keyEstItem]['tiene_atencion_total'] =true ;

                            }
                         
                        }
                        
                    }
                }

                $reservas = Reserva::where([['id_detalle_requerimiento',$detReq->id_detalle_requerimiento],['estado','!=',7],['id_guia_com_det',null],['id_guia_ven_det',null]])->get();
                foreach ($reservas as $keyRes => $res) {
                    foreach ($estadoItems as $keyEstItem => $estItem) {
                        if($estItem['id_detalle_requerimiento'] == $res->id_detalle_requerimiento){
                            $estadoItems[$keyEstItem]['cantidad_atendida_almacen'] += intval($res->stock_comprometido);
                            if($estadoItems[$keyEstItem]['cantidad_atendida_almacen']==$estItem['cantidad_solicitada']){
                                $estadoItems[$keyEstItem]['tiene_atencion_total'] =true ;

                            }
                         
                        }
                        
                    }
                }
            }
        }

        $detalleRequerimientoFiltrado=[];
        foreach ($estadoItems as $keyEstItem => $EstItemValue) {
            foreach ($detalleRequerimiento as $keyDetReq => $DetReqValue) {
                
                if($EstItemValue['id_detalle_requerimiento'] == $DetReqValue->id_detalle_requerimiento){
                    $detalleRequerimiento[$keyDetReq]['cantidad_atendida_almacen']=$EstItemValue['cantidad_atendida_almacen'];
                    $detalleRequerimiento[$keyDetReq]['cantidad_atendida_orden']=$EstItemValue['cantidad_atendida_orden'];
                    if($EstItemValue['tiene_atencion_total']==false){
                         $detalleRequerimientoFiltrado[]= $DetReqValue;
                    }
                }
            }
        }


       

        return response()->json([
            "success" => $isSuccess,
            "estado_item_list" => $estadoItems,
            "requerimiento" => $requerimiento,
            "detalle_requerimiento_list" => $detalleRequerimientoFiltrado,
            "mensaje" => '',
        ], 200);
    }
    

    public function obtenerProveedorEquivalente($idProveedorMgc, $razonSocialProveedorMgc){
    // public function obtenerProveedorEquivalente(Request $request){
        // $idProveedorMgc = $request->id_proveedor_mgc;
        // $razonSocialProveedorMgc = $request->razon_social_proveedor_mgc;

        $idProveedorAgile='';
        $idContribuyenteAgile='';
        $razonSocial='';
        $idTipoDocumento='';
        $descripcionTipoDocumento='';
        $numeroDocumento='';
        $direccionFiscal='';
        $idCuentaBancaria='';
        $numeroCuentaBancaria='';
        $numeroCuentaInterbancariaBancaria='';
        $idMonedaCuentaBancaria='';
        $simboloMonedaCuentaBancaria='';
        $idContacto='';
        $nombreContacto='';
        $telefonoContacto='';
        $cargoContacto='';

        if($idProveedorMgc >0){
           $proveedorMgc = CuadroCostoProveedor::find($idProveedorMgc);
        }elseif($razonSocialProveedorMgc !=null){
            $proveedorMgc = CuadroCostoProveedor::where('razon_social','LIKE','%'.$razonSocialProveedorMgc.'%')->first();
        }
        if($proveedorMgc){
            $contribuyenteAgile= Contribuyente::with("tipoDocumentoIdentidad")->where('razon_social','LIKE','%'.$proveedorMgc->razon_social.'%')->first();
            $cuentasContribuyenteAgile= CuentaContribuyente::with('moneda')->where([['id_contribuyente',$contribuyenteAgile->id_contribuyente],['estado',1]])->orderByRaw('por_defecto DESC, fecha_registro DESC')->first();
            $contactoContribuyenteAgile= ContactoContribuyente::where([['id_contribuyente',$contribuyenteAgile->id_contribuyente],['estado',1]])->orderByRaw('fecha_registro DESC')->first();

            $idContribuyenteAgile=$contribuyenteAgile->id_contribuyente;
            $razonSocial=$contribuyenteAgile->razon_social??'';
            $idTipoDocumento=$contribuyenteAgile->id_tipo_contribuyente;
            $descripcionTipoDocumento=$contribuyenteAgile->tipoDocumentoIdentidad!=null ? $contribuyenteAgile->tipoDocumentoIdentidad->descripcion:'';
            $numeroDocumento=$contribuyenteAgile->nro_documento??'';
            $direccionFiscal=$contribuyenteAgile->direccion_fiscal??'';

            if($cuentasContribuyenteAgile){
                $idCuentaBancaria= $cuentasContribuyenteAgile !=null ? $cuentasContribuyenteAgile->id_cuenta_contribuyente:'';
                $numeroCuentaBancaria= $cuentasContribuyenteAgile !=null ? $cuentasContribuyenteAgile->nro_cuenta:'';
                $numeroCuentaInterbancariaBancaria= $cuentasContribuyenteAgile !=null ? $cuentasContribuyenteAgile->nro_cuenta_interbancaria:'';
                $idMonedaCuentaBancaria= $cuentasContribuyenteAgile !=null ? $cuentasContribuyenteAgile->moneda->id_moneda:'';
                $simboloMonedaCuentaBancaria= $cuentasContribuyenteAgile !=null ? $cuentasContribuyenteAgile->moneda->simbolo:'';
            }

            if($contactoContribuyenteAgile){
                $idContacto = $contactoContribuyenteAgile->id_datos_contacto;
                $nombreContacto= $contactoContribuyenteAgile->nombre;
                $telefonoContacto=$contactoContribuyenteAgile->telefono;
                $cargoContacto=$contactoContribuyenteAgile->cargo;
            }

            if($contribuyenteAgile && $contribuyenteAgile->estado==0){
                $actualizarContribuyente= Contribuyente::find($contribuyenteAgile->id_contribuyente);
                $actualizarContribuyente->estado=1;
                $actualizarContribuyente->save();
            }

            $proveedorAgile= Proveedor::where('id_contribuyente',$contribuyenteAgile->id_contribuyente)->first();
           
            if($proveedorAgile !=null && isset($contribuyenteAgile->id_contribuyente)){
                $idProveedorAgile=$proveedorAgile->id_proveedor;

            }else{
                $nuevoProveedor= new Proveedor();
                $nuevoProveedor->id_contribuyente = $contribuyenteAgile->id_contribuyente;
                $nuevoProveedor->estado=1;
                $nuevoProveedor->fecha_registro=New Carbon();
                $nuevoProveedor->save();
                $idProveedorAgile=$nuevoProveedor->id_proveedor;                
            }
        }

        $data=[
            'id_proveedor'=>$idProveedorAgile,
            'id_contribuyente'=>$idContribuyenteAgile,
            'razon_social'=>$razonSocial,
            'id_tipo_documento'=>$idTipoDocumento,
            'descripcion_tipo_documento'=>$descripcionTipoDocumento,
            'nro_documento'=>$numeroDocumento,
            'direccion_fiscal'=>$direccionFiscal,
            'id_cuenta_bancaria'=>$idCuentaBancaria,
            'id_moneda_cuenta_bancaria'=>$idMonedaCuentaBancaria,
            'simbolo_moneda_cuenta_bancaria'=>$simboloMonedaCuentaBancaria,
            'numero_cuenta_bacnaria'=>$numeroCuentaBancaria,
            'numero_cuenta_interbacnaria'=>$numeroCuentaInterbancariaBancaria,
            'id_contacto'=>$idContacto,
            'nombre_contacto'=>$nombreContacto,
            'telefono_contacto'=>$telefonoContacto,
            'cargo_contacto'=>$cargoContacto,
        ];

        return $data;

    }


    public function guardarOrdenes(Request $request){

        DB::beginTransaction();
        try {
            $respuesta = '';
            $alerta = 'error';
            $mensaje='Sin acciones';
            $validatorErrors=[];
            // dd($request->all());
            // exit();

            $cantidadOrdenes = count($request->id_orden);
            // dd($cantidadOrdenes);
            // exit();
            $failValidacion = 0;
            for ($i = 0; $i < $cantidadOrdenes; $i++) {
                if(!(isset($request->id_empresa[$i]) && intval($request->id_empresa[$i])>0) ){
                    $validatorErrors[]='El campo empresa es requerido';
                    $failValidacion++;
                }
                if(!(isset($request->id_proveedor[$i]) && intval($request->id_proveedor[$i])>0) ){
                    $validatorErrors[]='El campo proveedor es requerido';
                    $failValidacion++;
                }
                if(!(isset($request->id_cuenta_proveedor[$i]) && intval($request->id_cuenta_proveedor[$i])>0)){
                    $validatorErrors[]='El campo cuenta bancaria de proveedor es requerido';
                    $failValidacion++;
                }
                if(!(isset($request->plazo_entrega[$i]) && intval($request->plazo_entrega[$i])>0)){
                    $validatorErrors[]='El campo plazo de entrega es requerido';
                    $failValidacion++;
                }

            }
            if($failValidacion>0){
                $alerta = 'warning';
                $respuesta = 'validación';
                $mensaje = 'Validación fallida';

                return response()->json(['alerta' => $alerta,  'respuesta' => $respuesta, 'mensaje' => $mensaje, 'validacion' => $validatorErrors]);

            }
            dd($failValidacion);
            exit();
            if($failValidacion==0){
                for($i =0;$i < $cantidadOrdenes; $i++){
                    if (preg_match('/[A-Za-z].*[0-9]|[0-9].*[A-Za-z]/', $request->id_orden[$i])){
                        $orden = new Orden();
                    }else{
                        $orden = Orden::firstOrNew(['id_orden_compra' => $request->id_orden[$i]]);
                    }
                    
                    $orden->id_tp_documento = isset($request->id_tipo_orden[$i])?$request->id_tipo_orden[$i]:null;
                    $orden->id_periodo = isset($request->id_periodo[$i])?$request->id_periodo[$i]:null;
                    $orden->fecha = isset($request->fecha_emision[$i])?$request->fecha_emision[$i]:null;
                    $orden->fecha_registro = new Carbon();
                    $orden->id_usuario = Auth::user()->id_usuario;
                    $orden->id_moneda = isset($request->id_moneda[$i])?$request->id_moneda[$i]:null;
                    $orden->incluye_igv = isset($request->incluye_igv[$i]) ? $request->incluye_igv[$i] : false;
                    $orden->incluye_icbper = isset($request->incluye_icbper[$i]) ? $request->incluye_icbper[$i] : false;
                    $orden->monto_subtotal = isset($request->monto_subtotal[$i])?$request->monto_subtotal[$i]:null;
                    $orden->monto_igv = isset($request->monto_igv[$i])?$request->monto_igv[$i]:null;
                    $orden->monto_total = isset($request->monto_total[$i])?$request->monto_total[$i]:null;
                    $orden->id_proveedor = isset($request->id_proveedor[$i])?$request->id_proveedor[$i]:null;
                    $orden->id_cta_principal = isset($request->id_cuenta_proveedor[$i]) ? $request->id_cuenta_proveedor[$i] : null;
                    $orden->id_contacto = isset($request->id_contacto_proveedor[$i]) ? $request->id_contacto_proveedor[$i] : null;
                    $orden->id_condicion_softlink = isset($request->id_condicion_softlink[$i]) ? $request->id_condicion_softlink[$i] : null;
                    $orden->plazo_dias = isset($request->plazo_dias[$i]) ? $request->plazo_dias[$i] : null;
                    $orden->id_condicion = isset($request->id_condicion_compra[$i]) ? $request->id_condicion_compra[$i] : null;
                    $orden->plazo_entrega =  isset($request->plazo_entrega[$i]) ? $request->plazo_entrega[$i] : null;
                    $orden->id_tp_doc = isset($request->id_tipo_documento[$i]) ? $request->id_tipo_documento[$i] : null;
                    $orden->personal_autorizado_1 = isset($request->personal_autorizado_1[$i]) ? $request->personal_autorizado_1[$i] : null;
                    $orden->personal_autorizado_2 = isset($request->personal_autorizado_2[$i]) ? $request->personal_autorizado_2[$i] : null;
                    $orden->id_sede = isset($request->id_sede[$i]) ? $request->id_sede[$i] : null;
                    $orden->direccion_destino = isset($request->descripcion_ubigeo_entrega[$i]) ? trim(strtoupper($request->descripcion_ubigeo_entrega[$i])) : null;
                    $orden->ubigeo_destino = isset($request->id_ubigeo_entrega[$i]) ? $request->id_ubigeo_entrega[$i] : null;
                    $orden->en_almacen = false;
                    $orden->estado = 1;
                    $orden->estado_pago = 1;
                    $orden->observacion = isset($request->observacion[$i]) ? trim(strtoupper($request->observacion[$i])) : null;
                    $orden->tipo_cambio_compra = isset($request->tipo_cambio[$i]) ? $request->tipo_cambio[$i] : true;
                    $orden->compra_local = isset($request->es_compra_local[$i]) ? $request->es_compra_local[$i] : false;
                    $orden->save();

    
                    if($orden->id_orden_compra > 0){
                        $mensaje = 'Se ha guardado la orden';
                        LogActividad::registrar(Auth::user(), 'Crear ordenes', 2, $orden->getTable(), null, $orden, '', 'Gestión de ordenes');
                    } 

                    $respuesta = 'ok';
                    $alerta = 'success';
                
                }
                DB::commit();
            
            } 
            return response()->json(['alerta' => $alerta,  'respuesta' => $respuesta, 'mensaje'=>$mensaje, 'validacion'=>[]],200);
            

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['alerta' => $alerta,   'respuesta' => $respuesta, 'mensaje' => 'Hubo un problema al intentar guardar. Mensaje de error: ' . $e->getMessage(), 'validacion'=>[]]);
        }

    }

    
}