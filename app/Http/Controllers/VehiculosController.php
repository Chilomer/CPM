<?php

namespace App\Http\Controllers;

use App\Models\Vehiculos;
use App\Http\Resources\VehiculosResource;
use Illuminate\Http\Request;


class VehiculosController extends Controller
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

        $vehiculos = Vehiculos::where('idempresa', $userid)->paginate(25);    

        return VehiculosResource::collection($vehiculos);
    }

    public function buscar(Request $request, $data)
    {  
        $user = auth('api')->user();
        $userid = $user->idempresa;

        $vehiculos = Vehiculos::where('idempresa', $userid)
        ->where(function ($query) use ($data) {
            $query->orWhere('descripcion', 'ilike', "%$data%")
            ->orWhere('placas', 'ilike', "%$data%");
        })
        // ->orWhere('clave', 'ilike', "%$data%")
        // ->orWhere('descripcion', 'ilike', "%$data%")
        ->get();    

        return VehiculosResource::collection($vehiculos);
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
        $vehiculos = $request->all();
        $user = auth('api')->user();
        $userid = $user->idempresa;

        if(isset($vehiculos['id'])){
            $vehiculosR = Vehiculos::where('id', $vehiculos['id'])->first();
            $vehiculosR->fill($vehiculos);
            $vehiculosR->save();
            // $vehiculosID = $vehiculos;            
        }else{
            $vehiculos['idempresa'] = $userid;
            $vehiculosR = Vehiculos::create($vehiculos);
            // $vehiculosID= Vehiculos::orderBy('id', "desc")->first();
        }
    
            VehiculosResource::withoutWrapping();
            return new VehiculosResource($vehiculosR);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $idinterno
     * @return \Illuminate\Http\Response
     */
    public function show($idinterno)
    {
        VehiculosResource::withoutWrapping();
        return new VehiculosResource(Vehiculos::find($idinterno));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Vehiculos  $vehiculos
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $idinterno)
    {
        $data = $request->all();

        $vehiculos = new Vehiculos();
        
        $vehiculosResponse = $vehiculos->createOrUpdate($data, $idinterno);
    
        VehiculosResource::withoutWrapping();
        return new VehiculosResource($vehiculosResponse);
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
        $vehiculos = Vehiculos::findOrFail($idinterno);
        if ($vehiculos !== null) {
            $vehiculos->fill($request->all());

                    $vehiculos->save();
            
                    return $this->show($vehiculos->idinterno);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Vehiculos  $vehiculos
     * @return \Illuminate\Http\Response
     */
    public function destroy(Vehiculos $vehiculos)
    {
        //
    }
}
