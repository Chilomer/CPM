<?php

namespace App\Models;

/**
 
 */
class CPOrigenDestino extends Model
{
    //
    protected $table = 'cporigendestino';

    // protected $casts = [
    //     'cancelado'                   => 'boolean'
    // ];

    protected $fillable = [   
        'usuariomod',
        'iddocumento',
        'idempresa',
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
        'regimenfiscal'
    ];


    /**
     * Método encargado de crear o actualizar una cporigendestino
     *
     * @param array $data Datos que desea crear o actualizar en el cporigendestino
     * @param string $idinterno
     *  Llave única del registro de la cporigendestino que desea editar
     * @param boolean $commitTransaction Parámetro que indica si deseas terminar la transacción
     *                                      o no, por default es <b>true</b>
     * @return CPOrigenDestino
     * @throws \Exception
     */
    public static function createOrUpdate($data, $idinterno = '', $commitTransaction = true)
    {
        static::beginTran();

        try {
            $cporigendestino = null;
            if (trim($idinterno) !== '') {
                $cporigendestino = CPOrigenDestino::findOrFail($idinterno);
            } else {
                $cporigendestino = new CPOrigenDestino();
            }

            $cporigendestino->fill($data);
    
            $cporigendestino->save();
            if ($commitTransaction) {
                static::commitTran();
            }
            
            return $cporigendestino;
        } catch (\Exception $e) {
            static::rollbackTran();
            throw $e;
        }
    }
}
