<?php

namespace App\Observers;

use App\Models\PreCPDetalle;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class PreCPDetalleObserver extends Observer
{
    public $rules = [
        'idprecp'   =>  'required|numeric',
        'idarticulo'   =>  'nullable|numeric',
        'descripcion'   =>  'required|string|max:100',
        'bienestransp'   =>  'required|string|max:100',//claveprodserv
        'unidad'   =>  'nullable|string|max:100',
        'claveunidad'   =>  'required|string|max:100',
        'materialpeligroso'   =>  'required|boolean',//se elige desde claveprodserv
        'clavematpeligroso'   =>  'nullable|string|max:100',
        'claveembalaje'   =>  'nullable|string|max:100',//desplegar descr
        'cantidad'   =>  'required|numeric',
        'dimensiones'   =>  'nullable|string|max:20',//30/40/30cm. longitud/altura/anchura
        'pesoenkg'   =>  'required|numeric',// peso total si son 10 piezas peso de las 10 piezas
        'valormercancia'   =>  'required|numeric',// valor total si son 10 piezas valor de las 10
        'moneda'   =>  'required|string|max:3',//tabla moneda
        'fraccionarancelaria'   =>  'nullable|string|max:100',
        'uuidcomercioext'   =>  'nullable|string|max:100',
        'pedimentos'   =>  'nullable|string|max:100'
    ];
    
    /**
     * Listen to the Empresa creating event.
     *
     * @param  PreCPDetalle  $precp
     * @return void
     */
    public function creating(PreCPDetalle $precpdetalle)
    {

    }

    /**
     * Listen to the PreCPDetalle saving event.
     *
     * @param  PreCPDetalle  $precpdetalle
     * @return void
     */
    public function saving(PreCPDetalle $precpdetalle)
    {
        

        if ($this->validate($precpdetalle))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Listen to the PreCPDetalle created event.
     *
     * @param  PreCPDetalle  $articulo
     * @return void
     */
    public function created(PreCPDetalle $precpdetalle)
    {
        //TODO crear registro en folios
    }

    /**
     * Listen to the PreCPDetalle deleting event.
     *
     * @param  PreCPDetalle  $precpdetalle
     * @return void
     */
    public function deleting(PreCPDetalle $precpdetalle)
    {
        //
    }
}