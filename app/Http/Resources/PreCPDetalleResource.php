<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class PreCPDetalleResource extends Resource
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
            'idprecp' => $this->idprecp, 
            'idarticulo' => $this->idarticulo, 
            'descripcion' => $this->descripcion, 
            'bienestransp' => $this->bienestransp, 
            'unidad' => $this->unidad, 
            'claveunidad' => $this->claveunidad, 
            'materialpeligroso' => $this->materialpeligroso,
            'clavematpeligroso' => $this->clavematpeligroso,
            'claveembalaje' => $this->claveembalaje, 
            'cantidad' => $this->fechaimporcantidadtacion,
            'dimensiones' => $this->dimensiones,
            // factor 2 corresponde a presentacion 3
            'pesoenkg' => $this->pesoenkg,
            'valormercancia' => $this->valormercancia,
            'moneda' => $this->moneda,
            'fraccionarancelaria' => $this->fraccionarancelaria,
            'uuidcomercioext' => $this->uuidcomercioext,
            'pedimentos' => $this->pedimentos,
            'clavearticulo' => $this->clavearticulo
        ];

        
        return $data;
    }
}
