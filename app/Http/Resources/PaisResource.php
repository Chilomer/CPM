<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class PaisResource extends Resource
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
            'clavepais' => $this->clavepais,
            'pais' => $this->pais,
        ];

        
        return $data;
    }
}
