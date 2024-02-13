<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class PreCPResource extends Resource
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
            'nombrefiscal' => $this->nombrefiscal, 
            'nombrecomercial' => $this->nombrecomercial, 
            'rfc' => $this->rfc, 
            'cp' => $this->cp, 
            'usocfdisat' => $this->usocfdisat, 
            'email' => $this->email, 
            'idempresadescarga' => $this->idempresadescarga,
            'fecha' => $this->fecha,
            'idcliente' => $this->idcliente, 
            'fechaimportacion' => $this->fechaimportacion,
            'codigo' => $this->codigo,
            // factor 2 corresponde a presentacion 3
            'numtotalmercancias' => $this->numtotalmercancias,
            'transpinternac' => $this->localnacionalinternacional,
            'entradasalidamerc' => $this->entradasalidamerc,
            'paisorigendestino' => $this->paisorigendestino,
            'viaentradasalida' => $this->viaentradasalida,
            'totaldistrec' => $this->totaldistrec,
            'pesobrutototal' => $this->pesobrutototal,
            'idsoftwarecarga' => $this->idsoftwarecarga,
            'idsoftwaredescarga' => $this->idsoftwaredescarga,
            'unidadpeso' => $this->unidadpeso
            
        ];

        
        return $data;
    }
}
