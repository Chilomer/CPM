<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class PersonalResource extends Resource
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
            'nombre' => $this->nombre,
            'email' => $this->email,
            'telefono' => $this->telefono,
            'tipousuario' => $this->tipousuario,
            'curp' => $this->curp,
            'numlicencia' => $this->numlicencia,
            'registrofiscal' => $this->registrofiscal,
            'paisresidencia' => $this->paisresidencia,
            'rfc' => $this->rfc,
            'calle' => $this->calle,
            'numext' => $this->numext,
            'numint' => $this->numint,
            'cp' => $this->cp,
            'colonia' => $this->colonia,
            'c_colonia' => $this->c_colonia,
            'estado' => $this->estado,
            'c_estado' => $this->c_estado,
            'clave_entfed' => $this->clave_entfed,
            'municipio' => $this->municipio,
            'c_municipio' => $this->c_municipio,
            'localidad' => $this->localidad,
            'c_localidad' => $this->c_localidad,
            'pais' => $this->pais,
            'tipofigura' => $this->tipofigura
            
        ];
        
        return $data;
    }
}
