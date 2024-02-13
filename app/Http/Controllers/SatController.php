<?php

namespace App\Http\Controllers;

use App\Models\Pais;
use App\Http\Resources\PaisResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {  
        
        $data = DB::table('codigopostalsat')->get();    

        return $data;
    }


    public function cp(Request $request, $cp)
    {  
        
        $data = DB::table('codigopostalsat')->where('c_codigopostal', $cp)->get();    

        return $data;
    }

    public function coloniasSat(Request $request, $cp)
    {  
        
        $data = DB::table('colonias_sat')->where('c_codigopostal', $cp)->get();    

        return $data;
    }

    public function localidadSat(Request $request, $cp)
    {  
        
        $data = DB::table('localidadSat')->where('c_estado', $cp)->get();    

        return $data;
    }

    public function cpPais(Request $request, $pais)
    {  
        
        $data = DB::table('codigopostalsat')->where('pais', $pais)->get();    

        return $data;
    }

    public function unidadPeso(Request $request, $search ='')
    {  
        
        $data = DB::table('claveunidadpesosat')
        ->orWhere('clave', 'ilike', "%$search%")
        ->orWhere('nombre', 'ilike', "%$search%")->get(); 

        return $data;
    }

    public function claveprodserv(Request $request, $search ='')
    {  
        
        $data = DB::table('claveprodservsat')
        ->orWhere('claveprodservsat', 'ilike', "%$search%")
        ->orWhere('descripcion', 'ilike', "%$search%")->get(); 

        return $data;
    }

    public function claveunidad(Request $request, $search ='')
    {  
        
        $data = DB::table('claveunidadsat')
        ->orWhere('claveunidadsat', 'ilike', "%$search%")
        ->orWhere('nombre', 'ilike', "%$search%")->get(); 

        return $data;
    }
    public function clavematpeligroso(Request $request, $search ='')
    {  
        
        $data = DB::table('clavematpeligrososat')
        ->orWhere('clavematpeligrososat', 'ilike', "%$search%")
        ->orWhere('descripcion', 'ilike', "%$search%")->get(); 

        return $data;
    }

    public function subtiporemolque(Request $request)
    {  
        
        $data = DB::table('subtiporemolquesat')->get(); 

        return $data;
    }

    public function tipopermiso(Request $request)
    {  
        
        $data = DB::table('tipopermisosctsat')->get(); 

        return $data;
    }

    public function configautotransporte(Request $request, $search ='')
    {  
        
        $data = DB::table('configautotranspfedsat')->get(); 

        return $data;
    }

    public function tipoembalajesat(Request $request, $search ='')
    {  
        
        $data = DB::table('tipoembalajesat')
        ->orWhere('claveembalajesat', 'ilike', "%$search%")
        ->orWhere('descripcion', 'ilike', "%$search%")->get(); 

        return $data;
    }

    public function moneda(Request $request)
    {  
        
        $data = DB::table('monedasat')->get(); 

        return $data;
    }

    public function parteTransporte(Request $request)
    {  
        
        $data = DB::table('partetransportesat')->get(); 

        return $data;
    }

    public function usocfdi(Request $request)
    {  
        
        $data = DB::table('usocfdisat')->get(); 

        return $data;
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $pais = new Pais();
        
            $pais->fill($request->all());
            $pais->Cancelado = 0;
                $pais->save();
        
                PaisResource::withoutWrapping();
                return new PaisResource($pais);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $idinterno
     * @return \Illuminate\Http\Response
     */
    public function show($idinterno)
    {
        PaisResource::withoutWrapping();
        return new PaisResource(Pais::find($idinterno));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Pais  $pais
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $idinterno)
    {
        $data = $request->all();

        $pais = new Pais();
        
        $paisResponse = $pais->createOrUpdate($data, $idinterno);
    
            PaisResource::withoutWrapping();
            return new PaisResource($paisResponse);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $idinterno
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idinterno)
    {
        $pais = Pais::findOrFail($idinterno);
        if ($pais !== null) {
            $pais->fill($request->all());

                    $pais->save();
            
                    return $this->show($pais->idinterno);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Pais  $pais
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pais $pais)
    {
        //
    }
}
