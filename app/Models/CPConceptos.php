<?php

namespace App\Models;

/**
 
 */
class CPConceptos extends Model
{
    //
    protected $table = 'conceptos';

    // protected $casts = [
    //     'cancelado'                   => 'boolean'
    // ];

    protected $fillable = [   
        'idempresa',
        'idprecp',
        'iddocumento',
        'tipodocto',
        'folio',
        'idcartaporte',
        'idarticulo',
        'clavearticulo',
        'descripcion',
        'presentacion',
        'cantidad',
        'precio',
        'descuento',
        'importe',
        'p_ieps',
        'p_iva',
        'partida',
        'peso',
        'claveprodserv',
        'claveunidad',
        'claveiva',
        'claveieps',
        'exento',
        'p_retencion_iva',
        'p_retencion_isr',
        'base_iva',
        'base_ieps',
        'importe_iva',
        'importe_ieps',
        'importe_ret_iva',
        'importe_ret_isr',
        'cancelado ',
        'unidad'
    ];


    /**
     * Método encargado de crear o actualizar una precpdetalle
     *
     * @param array $data Datos que desea crear o actualizar en el precpdetalle
     * @param string $idinterno
     *  Llave única del registro de la precpdetalle que desea editar
     * @param boolean $commitTransaction Parámetro que indica si deseas terminar la transacción
     *                                      o no,
     * 'por default es <b>true</b'>
     * @return CPConceptos
     * @throws \Exception
     */
    public static function createOrUpdate($data,
    $idinterno = '',
    $commitTransaction = true)
    {
        static::beginTran();

        try {
            $precpdetalle = null;
            if (trim($idinterno) !== '') {
                $precpdetalle = CPConceptos::findOrFail($idinterno);
            } else {
                $precpdetalle = new CPConceptos();
            }

            $precpdetalle->fill($data);
    
            $precpdetalle->save();
            if ($commitTransaction) {
                static::commitTran();
            }
            
            return $precpdetalle;
        } catch (\Exception $e) {
            static::rollbackTran();
            throw $e;
        }
    }
}
