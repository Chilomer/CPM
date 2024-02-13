<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class DocumentoResource extends Resource
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
            'idprecp' => $this->idprecp,
            'tipodocto' => $this->tipodocto,
            'folio' => $this->folio,
            'rfc' => $this->rfc,
            'nombre' => $this->nombre,
            'serie' => $this->serie,
            'tipocomprobante' => $this->tipocomprobante,
            'metodopago' => $this->metodopago,
            'tiporelacion' => $this->tiporelacion,
            'usocfdi' => $this->usocfdi,
            'versioncfdi' => $this->versioncfdi,
            'fecha' => $this->fecha,
            'fecha_vencimiento' => $this->fecha_vencimiento,
            'idvendedor' => $this->idvendedor,
            'sucursal' => $this->sucursal,
            'idcliente' => $this->idcliente,
            'peso' => $this->peso,
            'numcertificadosellodigital' => $this->numcertificadosellodigital,
            'subtotal' => $this->subtotal,
            'p_descuento' => $this->p_descuento,
            'descuento' => $this->descuento,
            'ieps' => $this->ieps,
            'iva' => $this->iva,
            'retencion_isr' => $this->retencion_isr,
            'retencion_iva' => $this->retencion_iva,
            'total' => $this->total,
            'valorpagado' => $this->valorpagado,
            'valorporcobrar' => $this->valorporcobrar,
            'cfdgenerado' => $this->cfdgenerado,
            'fechatimbrado' => $this->fechatimbrado,
            'cadena' => $this->cadena,
            'sellodigital' => $this->sellodigital,
            'sellosat' => $this->sellosat,
            'uuid' => $this->uuid,
            'localnacionalinternacional' => $this->localnacionalinternacional,
            'transporteinternacional' => $this->transporteinternacional,
            'entradasalidamerc' => $this->entradasalidamerc,
            'paisorigendestino' => $this->paisorigendestino,
            'viaentradasalida' => $this->viaentradasalida,
            'totaldistrec' => $this->totaldistrec,
            'numtotalmercancias' => $this->numtotalmercancias,
            'c_unidadpeso' => $this->c_unidadpeos,
            'idvehiculo' => $this->idvehiculo,
            'idremolque1' => $this->idremolque1,
            'idremolque2' => $this->idremolque2,
            'idpolizarespcivil' => $this->idpolizarespcivil,
            'idpolizamedambiente' => $this->idpolizamedambiente,
            'idpolizacarga' => $this->idpolizacarga,
        ];
        
        return $data;
    }
}
