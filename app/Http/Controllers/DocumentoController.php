<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Http\Resources\DocumentoResource;
use Illuminate\Http\Request;


class DocumentoController extends Controller
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
        
        $articulos = Documento::where('idempresa', $userid)->get();    

        return DocumentoResource::collection($articulos);
    }

    public function buscar(Request $request, $data)
    {  
        $user = auth('api')->user();
        $userid = $user->idempresa;

        $articulos = Documento::where('idempresa', $userid)
        ->where(function ($query) use ($data) {
            $query->orWhere('clave', 'ilike', "%$data%")
            ->orWhere('descripcion', 'ilike', "%$data%");
        })
        // ->orWhere('clave', 'ilike', "%$data%")
        // ->orWhere('descripcion', 'ilike', "%$data%")
        ->get();    

        return DocumentoResource::collection($articulos);
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
        $user = auth('api')->user();
        $userid = $user->idempresa;
        $articulos = new Documento();
        
            $articulos->fill($request->all());
            $articulos->idempresa = $userid;
                $articulos->save();
        
                DocumentoResource::withoutWrapping();
                return new DocumentoResource($articulos);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $idinterno
     * @return \Illuminate\Http\Response
     */
    public function show($idinterno)
    {
        DocumentoResource::withoutWrapping();
        return new DocumentoResource(Documento::find($idinterno));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Documento  $articulos
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $idinterno)
    {
        $data = $request->all();

        $articulos = new Documento();
        
        $articulosResponse = $articulos->createOrUpdate($data, $idinterno);
    
            DocumentoResource::withoutWrapping();
            return new DocumentoResource($articulosResponse);
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
        $articulos = Documento::findOrFail($idinterno);
        if ($articulos !== null) {
            $articulos->fill($request->all());

                    $articulos->save();
            
                    return $this->show($articulos->idinterno);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Documento  $articulos
     * @return \Illuminate\Http\Response
     */
    public function destroy(Documento $articulos)
    {
        //
    }
}
