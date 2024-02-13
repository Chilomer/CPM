<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class VehiculosResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            'id' => $this->id,
            'idempresa' => $this->idempresa,
            'tipovehiculo' => $this->tipovehiculo,
            'descripcion' => $this->descripcion,
            'clave' => $this->clave,
            'placas' => $this->placas,
            'capacidad' => $this->capacidad,
            'ano' => $this->ano,
            'tipopermisosct' => $this->tipopermisosct,
            'numpermiso' => $this->numpermiso,
            'configautotransportefed' => $this->configautotransportefed,
            'vehiculopropio' => $this->vehiculopropio,
            'idpropietario' => $this->idpropietario,
            'subtiporemolque' => $this->subtiporemolque,
            'idpolizarespcivil' => $this->idpolizarespcivil
        ];
        
        return $data;
    }
}
