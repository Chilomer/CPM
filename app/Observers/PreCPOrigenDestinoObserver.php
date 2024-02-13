<?php

namespace App\Observers;

use App\Models\PreCPOrigenDestino;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class PreCPOrigenDestinoObserver extends Observer
{
    public $rules = [
        'origendestino'   =>  'required|string|max:100',//Origen o Destino
        'orden'   =>  'nullable|numeric',
        'nombre'   =>  'nullable|string|max:100',
        'rfc'   =>  'required|string|max:100',//XEXX010101000 si es extranjero
        'numregidtrib'   =>  'nullable|string|max:100',//solo si es extranjero 
        'residenciafiscal'   =>  'nullable|string|max:100',//pais de residencia solo si extranjero
        'fechasalidallegada'   =>  'nullable|string|max:100',
        'horasalidallegada'   =>  'nullable|string|max:100',
        'iddirecciones'   =>  'nullable|string|max:100',
        'distanciarecorrida'   =>  'nullable|numeric',
        'calle'   =>  'required|string|max:100',
        'numext'   =>  'required|numeric',// 
        'numint'   =>  'nullable|numeric',// 
        'cp'   =>  'required|string|max:100',//req
        'colonia'   =>  'nullable|string|max:100',
        'c_colonia'   =>  'nullable|string|max:100',
        'estado'   =>  'required|string|max:100',//req
        'c_estado'   =>  'nullable|string|max:100',
        'clave_entfed'   =>  'nullable|string|max:100',
        'municipio'   =>  'nullable|string|max:100',
        'c_municipio'   =>  'nullable|string|max:100',
        'localidad'   =>  'nullable|string|max:100',
        'c_localidad'   =>  'nullable|string|max:100',
        'pais'   =>  'required|string|max:3',//req
        'referencia'   =>  'nullable|string|max:100',
        'idprecp'   =>  'required|numeric'
    ];
    
    /**
     * Listen to the Empresa creating event.
     *
     * @param  PreCPOrigenDestino  $precporigendestino
     * @return void
     */
    public function creating(PreCPOrigenDestino $precporigendestino)
    {

    }

    /**
     * Listen to the PreCPOrigenDestino saving event.
     *
     * @param  PreCPOrigenDestino  $precporigendestino
     * @return void
     */
    public function saving(PreCPOrigenDestino $precporigendestino)
    {
        

        if ($this->validate($precporigendestino))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Listen to the PreCPOrigenDestino created event.
     *
     * @param  PreCPOrigenDestino  $articulo
     * @return void
     */
    public function created(PreCPOrigenDestino $precporigendestino)
    {
        //TODO crear registro en folios
    }

    /**
     * Listen to the PreCPOrigenDestino deleting event.
     *
     * @param  PreCPOrigenDestino  $precporigendestino
     * @return void
     */
    public function deleting(PreCPOrigenDestino $precporigendestino)
    {
        //
    }
}