<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class AseguradorasResource extends Resource
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
            'aseguradora' => $this->aseguradora
        ];
        
        return $data;
    }
}
