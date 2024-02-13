<?php

namespace App\Http\Controllers;

use App\Models\RegimenFiscal;
use App\Http\Resources\RegimenFiscalResource;
use Illuminate\Http\Request;

class RegimenFiscalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {  
        
        $regimenfiscal = RegimenFiscal::get();    

        return RegimenFiscalResource::collection($regimenfiscal);
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
        $regimenfiscal = new RegimenFiscal();
        
            $regimenfiscal->fill($request->all());
            $regimenfiscal->Cancelado = 0;
                $regimenfiscal->save();
        
                RegimenFiscalResource::withoutWrapping();
                return new RegimenFiscalResource($regimenfiscal);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $idinterno
     * @return \Illuminate\Http\Response
     */
    public function show($idinterno)
    {
        RegimenFiscalResource::withoutWrapping();
        return new RegimenFiscalResource(RegimenFiscal::find($idinterno));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\RegimenFiscal  $regimenfiscal
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $idinterno)
    {
        $data = $request->all();

        $regimenfiscal = new RegimenFiscal();
        
        $regimenfiscalResponse = $regimenfiscal->createOrUpdate($data, $idinterno);
    
            RegimenFiscalResource::withoutWrapping();
            return new RegimenFiscalResource($regimenfiscalResponse);
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
        $regimenfiscal = RegimenFiscal::findOrFail($idinterno);
        if ($regimenfiscal !== null) {
            $regimenfiscal->fill($request->all());

                    $regimenfiscal->save();
            
                    return $this->show($regimenfiscal->idinterno);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\RegimenFiscal  $regimenfiscal
     * @return \Illuminate\Http\Response
     */
    public function destroy(RegimenFiscal $regimenfiscal)
    {
        //
    }
}
