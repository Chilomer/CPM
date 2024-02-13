<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class SoftwareResource extends Resource
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
            'nombresoftware' => $this->nombresoftware,
            'contactoprogramacion' => $this->contactoprogramacion,
            'emailcontactoprogramacion' => $this->emailcontactoprogramacion,
            'telefono' => $this->telefono,
            'cp' => $this->cp,
            'timbracp' => $this->timbracp,
            'publicar' => $this->publicar,
            'paginaweb' => $this->paginaweb,
            'emailpublicitario' => $this->emailpublicitario,
            'rfc' => $this->rfc,
            'nombrefiscal' => $this->nombrefiscal,
        ];
        
        return $data;
    }
}
