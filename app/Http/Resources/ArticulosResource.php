<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ArticulosResource extends Resource
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
            'clave' => $this->clave,
            'isbn' => $this->isbn,
            'iddepartamento' => $this->iddepartamento,
            'descripcion' => $this->descripcion,
            'presentacion' => $this->presentacion,
            'claveunidadsat' => $this->claveunidadsat,
            'claveprodservsat' => $this->claveprodservsat,
            'pesokgs' => $this->pesokgs,
            'materialpeligroso' => $this->materialpeligroso,
            'clavematpeligroso' => $this->clavematpeligroso,
            'claveembalaje' => $this->claveembalaje,
            'esconcepto' => $this->esconcepto,
            'dimensiones' => $this->dimensiones,
            'moneda' => $this->moneda,
            'unidadpeso' => $this->unidadpeso,
            'pesobruto' => $this->pesobruto,
            'pesoneto' => $this->pesoneto,
            'pesotara' => $this->pesotara,
        ];
        
        return $data;
    }
}
