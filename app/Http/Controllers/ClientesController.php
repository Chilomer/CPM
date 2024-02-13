<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use App\Models\Direcciones;
use App\Http\Resources\ClientesResource;
use Illuminate\Http\Request;


class ClientesController extends Controller
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

        $clientes = Clientes::where('idempresa', $userid)->paginate(25);    

        return ClientesResource::collection($clientes);
    }

    public function buscar(Request $request, $data)
    {  
        $user = auth('api')->user();
        $userid = $user->idempresa;

        $clientes = Clientes::where('idempresa', $userid)
        ->where(function ($query) use ($data) {
            $query->orWhere('nombre', 'ilike', "%$data%")
            ->orWhere('nombrecomercial', 'ilike', "%$data%");
        })
        // ->orWhere('clave', 'ilike', "%$data%")
        // ->orWhere('descripcion', 'ilike', "%$data%")
        ->get();    

        return ClientesResource::collection($clientes);
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
        $cliente = $request->cliente;
        $direcciones = $request->direcciones;
        $user = auth('api')->user();
        $userid = $user->idempresa;

        if(isset($cliente['id'])){
            $clientes = Clientes::where('id', $cliente['id'])->first();
            $clientes->fill($cliente);
            $clientes->save();
            $clientesID = $clientes;            
        }else{
            $cliente['idempresa'] = $userid;
            $clientes = Clientes::create($cliente);
            $clientesID= Clientes::orderBy('id', "desc")->first();
        }
        
        // $clientes = new Clientes();
        
        // $clientes->fill($cliente);
        // $clientes->save();
        Direcciones::where('idcliente', $clientesID->id)->delete();
        foreach($direcciones as $key => $direccion){
            $direccion['idcliente'] = $clientesID->id;
            $direccionRequest = new Direcciones();
            $direccionRequest->fill($direccion);
            $direccionRequest->save();
            
        }
    
            ClientesResource::withoutWrapping();
            return new ClientesResource($clientes);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $idinterno
     * @return \Illuminate\Http\Response
     */
    public function show($idinterno)
    {
        ClientesResource::withoutWrapping();
        return new ClientesResource(Clientes::find($idinterno));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Clientes  $clientes
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $idinterno)
    {
        $data = $request->all();

        $clientes = new Clientes();
        
        $clientesResponse = $clientes->createOrUpdate($data, $idinterno);
    
        ClientesResource::withoutWrapping();
        return new ClientesResource($clientesResponse);
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
        $clientes = Clientes::findOrFail($idinterno);
        if ($clientes !== null) {
            $clientes->fill($request->all());

                    $clientes->save();
            
                    return $this->show($clientes->idinterno);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Clientes  $clientes
     * @return \Illuminate\Http\Response
     */
    public function destroy(Clientes $clientes)
    {
        //
    }
}
