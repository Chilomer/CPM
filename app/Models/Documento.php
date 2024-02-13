<?php

namespace App\Models;

/**
 
 */
class Documento extends Model
{
    //
    protected $table = 'documento';

    // protected $casts = [
    //     'cancelado'                   => 'boolean'
    // ];

    protected $fillable = [   
        'idempresa',
        'iddocumento',
        'tipodocto',
        'folio',
        'serie',
        'tipocomprobante',
        'metodopago',
        'tiporelacion',
        'usocfdi',
        'versioncfdi',
        'fecha',
        'fecha_vencimiento',
        'idvendedor',
        'sucursal',
        'idcliente',
        'peso',
        'numcertificadosellodigital',
        'subtotal',
        'p_descuento',
        'descuento',
        'ieps',
        'iva',
        'retencion_isr',
        'retencion_iva',
        'total',
        'valorpagado',
        'valorporcobrar',
        'cfdgenerado',
        'fechatimbrado',
        'cadena',
        'sellodigital',
        'sellosat',
        'uuid',
        'localnacionalinternacional',
        'transporteinternacional',
        'entradasalidamerc',
        'paisorigendestino',
        'viaentradasalida',
        'totaldistrec',
        'numtotalmercancias',
        'c_unidadpeso',
        'idvehiculo',
        'idremolque1',
        'idremolque2',
        'idpolizarespcivil',
        'idpolizamedambiente',
        'idpolizacarga',
        'rfc',
        'nombre',
        'codigopostal',
        'regimenfiscal',
        'formapago',
        'idchofer',
        'idreferencia',
        'usuariosw',
        'passsw'
    ];


    /**
     * Método encargado de crear o actualizar una documento
     *
     * @param array $data Datos que desea crear o actualizar en el documento
     * @param string $idinterno
     *  Llave única del registro de la documento que desea editar
     * @param boolean $commitTransaction Parámetro que indica si deseas terminar la transacción
     *                                      o no, por default es <b>true</b>
     * @return Documento
     * @throws \Exception
     */
    public static function createOrUpdate($data, $idinterno = '', $commitTransaction = true)
    {
        static::beginTran();

        try {
            $documento = null;
            if (trim($idinterno) !== '') {
                $documento = Documento::findOrFail($idinterno);
            } else {
                $documento = new Documento();
            }

            $documento->fill($data);
    
            $documento->save();
            if ($commitTransaction) {
                static::commitTran();
            }
            
            return $documento;
        } catch (\Exception $e) {
            static::rollbackTran();
            throw $e;
        }
    }
}
