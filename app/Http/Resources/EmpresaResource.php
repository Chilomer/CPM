<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class EmpresaResource extends Resource
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
            'fechamod' => $this->FechaMod,
            'regimenfiscal' => $this->regimenfiscal,
            'nombrefiscal' => $this->nombrefiscal, 
            'nombrecomercial' => $this->nombrecomercial, 
            'representante' => $this->representante, 
            'telefonoempresa' => $this->telefonoempresa, 
            'identificador' => $this->identificador, 
            'contrasena' => $this->contrasena, 
            'email' => $this->email,
            'rfc' => $this->rfc,
            'calle' => $this->calle, 
            'numext' => $this->numext,
            'numint' => $this->numint,
            // factor 2 corresponde a presentacion 3
            'colonia' => $this->colonia,
            'localidad' => $this->localidad,
            'municipio' => $this->municipio,
            'cp' => $this->cp,
            'estado' => $this->estado,
            'pais' => $this->pais,
            'giro' => $this->giro,
            'tipousuario' => $this->tipousuario,
            'tipoempresa' => $this->tipoempresa,
            'fechaalta' => $this->fechaalta,
            'cancelado' => $this->cancelado,
            'fechacancelado' => $this->fechacancelado
        ];

        
        return $data;
    }
}
