<?php

namespace App\Models;

/**
 
 */
class PreCPOrigenDestino extends Model
{
    //
    protected $table = 'precporigendestino';

    // protected $casts = [
    //     'cancelado'                   => 'boolean'
    // ];

    protected $fillable = [   
        'usuariomod',
        'origendestino',
        'orden',
        'nombre',
        'rfc',
        'numregidtrib',
        'residenciafiscal',
        'fechasalidallegada',
        'horasalidallegada',
        'iddirecciones',
        'distanciarecorrida',
        'calle',
        'numext',
        'numint',
        'cp',
        'colonia',
        'c_colonia',
        'estado',
        'c_estado',
        'clave_entfed',
        'municipio',
        'c_municipio',
        'localidad',
        'c_localidad',
        'pais',
        'referencia',
        'idprecp',
        'regimenfiscal'
    ];


    /**
     * Método encargado de crear o actualizar una precporigendestino
     *
     * @param array $data Datos que desea crear o actualizar en el precporigendestino
     * @param string $idinterno
     *  Llave única del registro de la precporigendestino que desea editar
     * @param boolean $commitTransaction Parámetro que indica si deseas terminar la transacción
     *                                      o no, por default es <b>true</b>
     * @return PreCPOrigenDestino
     * @throws \Exception
     */
    public static function createOrUpdate($data, $idinterno = '', $commitTransaction = true)
    {
        static::beginTran();

        try {
            $precporigendestino = null;
            if (trim($idinterno) !== '') {
                $precporigendestino = PreCPOrigenDestino::findOrFail($idinterno);
            } else {
                $precporigendestino = new PreCPOrigenDestino();
            }

            $precporigendestino->fill($data);
    
            $precporigendestino->save();
            if ($commitTransaction) {
                static::commitTran();
            }
            
            return $precporigendestino;
        } catch (\Exception $e) {
            static::rollbackTran();
            throw $e;
        }
    }
}
