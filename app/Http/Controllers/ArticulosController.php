<?php

namespace App\Http\Controllers;

use App\Models\Articulos;
use App\Http\Resources\ArticulosResource;
use Illuminate\Http\Request;


class ArticulosController extends Controller
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
        
        $articulos = Articulos::where('idempresa', $userid)->get();    

        return ArticulosResource::collection($articulos);
    }
    
    public function indexConcepto(Request $request)
    {  
        $user = auth('api')->user();
        $userid = $user->idempresa;
        
        $articulos = Articulos::where('idempresa', $userid)
        ->where('esconcepto', 1)->get();    

        return ArticulosResource::collection($articulos);
    }

    public function buscar(Request $request, $data)
    {  
        $user = auth('api')->user();
        $userid = $user->idempresa;

        $articulos = Articulos::where('idempresa', $userid)
        ->where(function ($query) use ($data) {
            $query->orWhere('clave', 'ilike', "%$data%")
            ->orWhere('descripcion', 'ilike', "%$data%");
        })
        // ->orWhere('clave', 'ilike', "%$data%")
        // ->orWhere('descripcion', 'ilike', "%$data%")
        ->get();    

        return ArticulosResource::collection($articulos);
    }

    public function buscarConcepto(Request $request, $data)
    {  
        $user = auth('api')->user();
        $userid = $user->idempresa;

        $articulos = Articulos::where('idempresa', $userid)
        ->where('esconcepto', '1')
        ->where(function ($query) use ($data) {
            $query->orWhere('clave', 'ilike', "%$data%")
            ->orWhere('descripcion', 'ilike', "%$data%");
        })
        // ->orWhere('clave', 'ilike', "%$data%")
        // ->orWhere('descripcion', 'ilike', "%$data%")
        ->get();    

        return ArticulosResource::collection($articulos);
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
        $articulos = new Articulos();
        
            $articulos->fill($request->all());
            $articulos->idempresa = $userid;
                $articulos->save();
        
                ArticulosResource::withoutWrapping();
                return new ArticulosResource($articulos);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $idinterno
     * @return \Illuminate\Http\Response
     */
    public function show($idinterno)
    {
        ArticulosResource::withoutWrapping();
        return new ArticulosResource(Articulos::find($idinterno));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Articulos  $articulos
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $idinterno)
    {
        $data = $request->all();

        $articulos = new Articulos();
        
        $articulosResponse = $articulos->createOrUpdate($data, $idinterno);
    
            ArticulosResource::withoutWrapping();
            return new ArticulosResource($articulosResponse);
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
        $articulos = Articulos::findOrFail($idinterno);
        if ($articulos !== null) {
            $articulos->fill($request->all());

                    $articulos->save();
            
                    return $this->show($articulos->idinterno);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Articulos  $articulos
     * @return \Illuminate\Http\Response
     */
    public function destroy(Articulos $articulos)
    {
        //
    }
}
