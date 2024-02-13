<?php

namespace App\Observers;

use App\Models\Empresa;
use App\Models\Folios;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class EmpresaObserver extends Observer
{
    public $rules = [
        'regimenfiscal' =>  'required|string|max:255',
        'nombrefiscal' =>  'required|string|max:255',
        'nombrecomercial' =>  'nullable|string|max:255',
        'representante' =>  'nullable|string|max:100',
        'telefonoempresa' =>  'required|string|max:100',
        'identificador' =>  'required|string|max:100',
        'contrasena' =>  'required|string|max:100',
        'email' =>  'required|string|max:100',
        'rfc' =>  'required|string|max:100',
        'calle' =>  'nullable|string|max:100',
        'numext' =>  'nullable|numeric',
        'numint' =>  'nullable|numeric',
        'colonia' =>  'nullable|string|max:100',
        'localidad' =>  'nullable|string|max:100',
        'municipio' =>  'nullable|string|max:100',
        'cp' =>  'nullable|numeric',
        'estado' =>  'nullable|string|max:100',
        'pais' =>  'nullable|string|max:100',
        'giro' =>  'nullable|string|max:100',
        'tipousuario' =>  'required|string|max:100',
        'tipoempresa' =>  'required|string|max:100',
        'fechaalta' =>  'nullable|string|max:100',
        'cancelado' =>  'required|boolean',
        'fechacancelado' =>  'nullable|string|max:100',
    ];
    
    /**
     * Listen to the Empresa creating event.
     *
     * @param  Empresa  $empresa
     * @return void
     */
    public function creating(Empresa $empresa)
    {
        $empresa->fechaalta = new Carbon();
    }

    /**
     * Listen to the Empresa saving event.
     *
     * @param  Empresa  $empresa
     * @return void
     */
    public function saving(Empresa $empresa)
    {
        if ($this->validate($empresa))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Listen to the Empresa created event.
     *
     * @param  Empresa  $articulo
     * @return void
     */
    public function created(Empresa $empresa)
    {
        //TODO crear registro en folios
        
        // $data = [
        //     'idempresa' => $empresa->id,
        //     'foliostimbres' => 0,
        //     'foliosprecp' => 10
        // ];
        // $folio = new Folios();

        // $folio->fill($data);
        // $folio->save();

    }

    /**
     * Listen to the Empresa deleting event.
     *
     * @param  Empresa  $empresa
     * @return void
     */
    public function deleting(Empresa $empresa)
    {
        //
    }
}