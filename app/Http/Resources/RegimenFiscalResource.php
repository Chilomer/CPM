<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class RegimenFiscalResource extends Resource
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
            'claveregimenfiscal' => $this->claveregimenfiscal,
            'descripcion' => $this->descripcion,
        ];

        
        return $data;
    }
}
