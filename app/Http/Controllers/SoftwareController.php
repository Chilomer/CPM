<?php

namespace App\Http\Controllers;

use App\Models\Software;
use App\Http\Resources\SoftwareResource;
use Illuminate\Http\Request;


class SoftwareController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {  
        
        $software = Software::get();    

        return SoftwareResource::collection($software);
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
        $software = new Software();
        
            $software->fill($request->all());
                $software->save();
        
                SoftwareResource::withoutWrapping();
                return new SoftwareResource($software);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $idinterno
     * @return \Illuminate\Http\Response
     */
    public function show($idinterno)
    {
        SoftwareResource::withoutWrapping();
        return new SoftwareResource(Software::find($idinterno));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Software  $software
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $idinterno)
    {
        $data = $request->all();

        $software = new Software();
        
        $softwareResponse = $software->createOrUpdate($data, $idinterno);
    
            SoftwareResource::withoutWrapping();
            return new SoftwareResource($softwareResponse);
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
        $software = Software::findOrFail($idinterno);
        if ($software !== null) {
            $software->fill($request->all());

                    $software->save();
            
                    return $this->show($software->idinterno);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Software  $software
     * @return \Illuminate\Http\Response
     */
    public function destroy(Software $software)
    {
        //
    }
}
