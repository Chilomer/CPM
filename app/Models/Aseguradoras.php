<?php

namespace App\Models;

/**
 
 */
class Aseguradoras extends Model
{
    //
    protected $table = 'aseguradoras';

    protected $casts = [
        'id' => 'string'
    ];

    protected $fillable = [
        'idempresa', 
        'aseguradora'
    ];


    /**
     * Método encargado de crear o actualizar una empresa
     *
     * @param array $data Datos que desea crear o actualizar en el empresa
     * @param string $idinterno
     *  Llave única del registro de la empresa que desea editar
     * @param boolean $commitTransaction Parámetro que indica si deseas terminar la transacción
     *                                      o no, por default es <b>true</b>
     * @return Aseguradoras
     * @throws \Exception
     */
    public static function createOrUpdate($data, $idinterno = '', $commitTransaction = true)
    {
        static::beginTran();

        try {
            $software = null;
            if (trim($idinterno) !== '') {
                $software = Aseguradoras::findOrFail($idinterno);
            } else {
                $software = new Aseguradoras();
            }

            $software->fill($data);
    
            $software->save();
            if ($commitTransaction) {
                static::commitTran();
            }
            return $software;
        } catch (\Exception $e) {
            static::rollbackTran();
            throw $e;
        }
    }
}
