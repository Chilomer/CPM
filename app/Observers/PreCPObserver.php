<?php

namespace App\Observers;

use App\Models\PreCP;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class PreCPObserver extends Observer
{
    public $rules = [
    'nombrefiscal'   =>  'required|string|max:100',
    'nombrecomercial'   =>  'nullable|string|max:100',
    'rfc'   =>  'required|string|max:13',
    'cp'   =>  'nullable|string|max:5',
    'usocfdisat'   =>  'required|string|max:100',
    'email'   =>  'required|string|max:100',
    'idempresadescarga'   =>  'nullable|numeric',
    'idcliente'   =>  'nullable|numeric',
    'codigo'   =>  'nullable|string|max:100',
    'numtotalmercancias'   =>  'required|numeric',
    'transpinternac' => 'required|string|max:100',
    'entradasalidamerc'   =>  'nullable|string|max:100', //si es internacional entrada o salida
    'paisorigendestino'   =>  'nullable|string|max:100',// si entrada pais de origen, si salida pais destino
    'viaentradasalida'   =>  'nullable|string|max:100', //01 autotransporte
    'totaldistrec'   =>  'nullable|string|max:100',
    'idsoftware'   =>  'nullable|string|max:100',
    'unidadpeso'   =>  'nullable|string|max:100'
    ];
    
    /**
     * Listen to the Empresa creating event.
     *
     * @param  PreCP  $precp
     * @return void
     */
    public function creating(PreCP $precp)
    {
        $precp->fecha = new Carbon();
    }

    /**
     * Listen to the PreCP saving event.
     *
     * @param  PreCP  $precp
     * @return void
     */
    public function saving(PreCP $precp)
    {


        if ($this->validate($precp))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Listen to the PreCP created event.
     *
     * @param  PreCP  $articulo
     * @return void
     */
    public function created(PreCP $precp)
    {
        //TODO crear registro en folios
    }

    /**
     * Listen to the PreCP deleting event.
     *
     * @param  PreCP  $precp
     * @return void
     */
    public function deleting(PreCP $precp)
    {
        //
    }
}