<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ClientesResource extends Resource
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
            'nombrecomercial' => $this->nombrecomercial,
            'direcciones' => $this->direcciones()
        ];
        
        return $data;
    }
}
