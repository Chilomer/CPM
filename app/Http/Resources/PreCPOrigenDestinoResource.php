<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class PreCPOrigneDestinoResource extends Resource
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
            'fechamod' => $this->fechamod,
            'usuariomod' => $this->usuariomod,
            'origendestino' => $this->idprecp, //Origen o Destino
            'orden' => $this->orden, 
            'nombre' => $this->nombre, 
            'rfc' => $this->rfc, 
            'numregidtrib' => $this->numregidtrib, //si es usa es obligatorio
            'residenciafiscal' => $this->residenciafiscal, 
            'fechasalidallegada' => $this->fechasalidallegada,
            'horasalidallegada' => $this->horasalidallegada,
            'iddirecciones' => $this->iddirecciones, 
            'distanciarecorrida' => $this->distanciarecorrida,
            'calle' => $this->calle,
            // factor 2 corresponde a presentacion 3
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
            'referencia' => $this->referencia,
            'idprecp' => $this->idprecp
        ];

        
        return $data;
    }
}
