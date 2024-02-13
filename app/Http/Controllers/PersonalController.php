<?php

namespace App\Http\Controllers;

use App\Models\Personal;
use App\Models\Direcciones;
use App\Http\Resources\PersonalResource;
use Illuminate\Http\Request;


class PersonalController extends Controller
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

        $personal = Personal::where('idempresa', $userid)->paginate(25);    

        return PersonalResource::collection($personal);
    }

    public function buscar(Request $request, $data)
    {  
        $user = auth('api')->user();
        $userid = $user->idempresa;

        $personal = Personal::where('idempresa', $userid)
        ->where(function ($query) use ($data) {
            $query->orWhere('nombre', 'ilike', "%$data%")
            ->orWhere('rfc', 'ilike', "%$data%");
        })
        // ->orWhere('clave', 'ilike', "%$data%")
        // ->orWhere('descripcion', 'ilike', "%$data%")
        ->get();    

        return PersonalResource::collection($personal);
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
        $personal = $request->all();
        $user = auth('api')->user();
        $userid = $user->idempresa;

        if(isset($personal['id'])){
            $personalR = Personal::where('id', $personal['id'])->first();
            $personalR->fill($personal);
            $personalR->save();
            // $personalID = $personal;            
        }else{
            $personal['idempresa'] = $userid;
            $personalR = Personal::create($personal);
            // $personalID= Personal::orderBy('id', "desc")->first();
        }
    
            PersonalResource::withoutWrapping();
            return new PersonalResource($personalR);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $idinterno
     * @return \Illuminate\Http\Response
     */
    public function show($idinterno)
    {
        PersonalResource::withoutWrapping();
        return new PersonalResource(Personal::find($idinterno));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Personal  $personal
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $idinterno)
    {
        $data = $request->all();

        $personal = new Personal();
        
        $personalResponse = $personal->createOrUpdate($data, $idinterno);
    
        PersonalResource::withoutWrapping();
        return new PersonalResource($personalResponse);
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
        $personal = Personal::findOrFail($idinterno);
        if ($personal !== null) {
            $personal->fill($request->all());

                    $personal->save();
            
                    return $this->show($personal->idinterno);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Personal  $personal
     * @return \Illuminate\Http\Response
     */
    public function destroy(Personal $personal)
    {
        //
    }
}
