<?php

namespace App\Http\Controllers;

use App\Models\Pais;
use App\Http\Resources\PaisResource;
use Illuminate\Http\Request;


class PaisController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {  
        
        $pais = Pais::get();    

        return PaisResource::collection($pais);
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
