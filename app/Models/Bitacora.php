<?php

namespace App\Models;

/**
 
 */
class Bitacora extends Model
{
    //
    protected $table = 'bitacora';

    protected $casts = [
    ];

    protected $fillable = [
        'idempresa',
        'tipo', 
        'fecha',
        'idprecp',
        'iddoccartaporte',
        'observacion',
        'idsoftware'
    ];


    /**
     * Método encargado de crear o actualizar una empresa
     *
     * @param array $data Datos que desea crear o actualizar en el empresa
     * @param string $idinterno
     *  Llave única del registro de la empresa que desea editar
     * @param boolean $commitTransaction Parámetro que indica si deseas terminar la transacción
     *                                      o no, por default es <b>true</b>
     * @return Bitacora
     * @throws \Exception
     */
    public static function createOrUpdate($data, $idinterno = '', $commitTransaction = true)
    {
        static::beginTran();

        try {
            $bitacora = null;
            if (trim($idinterno) !== '') {
                $bitacora = Bitacora::findOrFail($idinterno);
            } else {
                $bitacora = new Bitacora();
            }

            $bitacora->fill($data);
    
            $bitacora->save();
            if ($commitTransaction) {
                static::commitTran();
            }
            return $bitacora;
        } catch (\Exception $e) {
            static::rollbackTran();
            throw $e;
        }
    }
}
