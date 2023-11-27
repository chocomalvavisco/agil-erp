<?php

namespace App\Http\Controllers;

use App\Models\contabilidad\ContribuyenteView;
use App\Models\Contabilidad\CuentaContribuyente;
use App\Models\Rrhh\CuentaPersona;
use App\Models\Rrhh\PersonaView;
use Illuminate\Http\Request;

class NecesidadesController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        //
    }

    function view_main_necesidades()
    {
        return view('necesidades.main');
    }


    public function listaDestinatarioPersona(){
        $data= PersonaView::all();
        $output['data'] = $data;

        return response()->json($output);
    }
    public function obtenerDataCuentasDePersona($idPersona){
        $data= CuentaPersona::where([['id_persona',$idPersona],['estado','!=',7]])->get();
        return  $data;
    }
    
    public function listaDestinatarioContribuyente(){
        $data= ContribuyenteView::where('tipo','=','PROVEEDOR')->get();
        $output['data'] = $data;
        return response()->json($output);
        
    }

    public function obtenerDataCuentasDeContribuyente($idContribuyente){
        $data= CuentaContribuyente::where([['id_contribuyente',$idContribuyente],['estado','!=',7]])->get();
        return  $data;
    }
}
