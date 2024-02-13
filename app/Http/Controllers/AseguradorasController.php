<?php

namespace App\Http\Controllers;

use App\Models\Aseguradoras;
use App\Models\Polizas;
use App\Http\Resources\AseguradorasResource;
use App\Http\Resources\PolizasResource;
use Illuminate\Http\Request;


class AseguradorasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {  
        $user = auth('api')->user();
        $userid = $user->idempresa;

        $aseguradoras = Aseguradoras::where('idempresa', $userid)->get();    

        return AseguradorasResource::collection($aseguradoras);
    }

    public function indexPoliza(Request $request)
    {  
        $user = auth('api')->user();
        $userid = $user->idempresa;

        $polizas = Polizas::where('idempresa', $userid)->get();    

        return PolizasResource::collection($polizas);
    }

    public function buscar(Request $request, $data)
    {  
        $user = auth('api')->user();
        $userid = $user->idempresa;

        $aseguradoras = Polizas::selectRaw('aseguradoras.aseguradora, polizas.id as idpoliza, polizas.tipopoliza, polizas.poliza as poliza')
        ->join('aseguradoras', 'aseguradoras.id', '=', 'polizas.idaseguradora')
        ->where('aseguradoras.idempresa', $userid)
        ->where(function ($query) use ($data) {
            $query->orWhere('aseguradoras.aseguradora', 'ilike', "%$data%")
            ->orWhere('polizas.poliza', 'ilike', "%$data%");
        })
        // ->orWhere('clave', 'ilike', "%$data%")
        // ->orWhere('descripcion', 'ilike', "%$data%")
        ->get();    

        return [
            "data" => $aseguradoras
        ];
    }

    public function buscaTodo(Request $request)
    {  
        $user = auth('api')->user();
        $userid = $user->idempresa;

        $aseguradoras = Polizas::selectRaw('aseguradoras.aseguradora, polizas.id as idpoliza, polizas.tipopoliza, polizas.poliza as poliza')
        ->join('aseguradoras', 'aseguradoras.id', '=', 'polizas.idaseguradora')
        ->where('aseguradoras.idempresa', $userid)
        // ->orWhere('clave', 'ilike', "%$data%")
        // ->orWhere('descripcion', 'ilike', "%$data%")
        ->get();    

        return [
            "data" => $aseguradoras
        ];
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
        $aseguradora = $request->all();
        $user = auth('api')->user();
        $userid = $user->idempresa;

        if(isset($aseguradora['id'])){
            $aseguradoras = Aseguradoras::where('id', $aseguradora['id'])->first();
            $aseguradoras->fill($aseguradora);
            $aseguradoras->save();          
        }else{
            $aseguradora['idempresa'] = $userid;
            $aseguradoras = Aseguradoras::create($aseguradora);
        }
    
            AseguradorasResource::withoutWrapping();
            return new AseguradorasResource($aseguradoras);
    }

    public function poliza(Request $request)
    {
        $poliza = $request->all();
        $user = auth('api')->user();
        $userid = $user->idempresa;

        if(isset($poliza['id'])){
            $polizas = Polizas::where('id', $poliza['id'])->first();
            $polizas->fill($poliza);
            $polizas->save();          
        }else{
            $poliza['idempresa'] = $userid;
            $polizas = Polizas::create($poliza);
        }
    
            PolizasResource::withoutWrapping();
            return new PolizasResource($polizas);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $idinterno
     * @return \Illuminate\Http\Response
     */
    public function show($idinterno)
    {
        AseguradorasResource::withoutWrapping();
        return new AseguradorasResource(Aseguradoras::find($idinterno));
    }

    public function showPoliza($idinterno)
    {
        PolizasResource::withoutWrapping();
        return new PolizasResource(Polizas::find($idinterno));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Aseguradoras  $aseguradoras
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $idinterno)
    {
        $data = $request->all();

        $aseguradoras = new Aseguradoras();
        
        $aseguradorasResponse = $aseguradoras->createOrUpdate($data, $idinterno);
    
        AseguradorasResource::withoutWrapping();
        return new AseguradorasResource($aseguradorasResponse);
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
        $aseguradoras = Aseguradoras::findOrFail($idinterno);
        if ($aseguradoras !== null) {
            $aseguradoras->fill($request->all());

                    $aseguradoras->save();
            
                    return $this->show($aseguradoras->idinterno);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Aseguradoras  $aseguradoras
     * @return \Illuminate\Http\Response
     */
    public function destroy(Aseguradoras $aseguradoras)
    {
        //
    }
}
