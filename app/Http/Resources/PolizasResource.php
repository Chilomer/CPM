<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class PolizasResource extends Resource
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
            'idaseguradora' => $this->idaseguradora,
            'tipopoliza' => $this->tipopoliza,
            'poliza' => $this->poliza,
            'vencimiento' => $this->vencimiento,
            'primaseguro' => $this->primaseguro,
            'aseguradora' => $this->aseguradora()
        ];
        
        return $data;
    }
}
