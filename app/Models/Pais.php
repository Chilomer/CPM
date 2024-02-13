<?php

namespace App\Models;

/**
 
 */
class Pais extends Model
{
    //
    protected $table = 'paissat';

    protected $casts = [
    ];

    protected $fillable = [
        'id', 
        'clavepais', 
        'pais'
    ];


    /**
     * Método encargado de crear o actualizar una empresa
     *
     * @param array $data Datos que desea crear o actualizar en el empresa
     * @param string $idinterno
     *  Llave única del registro de la empresa que desea editar
     * @param boolean $commitTransaction Parámetro que indica si deseas terminar la transacción
     *                                      o no, por default es <b>true</b>
     * @return Pais
     * @throws \Exception
     */
    public static function createOrUpdate($data, $idinterno = '', $commitTransaction = true)
    {
        static::beginTran();

        try {
            $pais = null;
            if (trim($idinterno) !== '') {
                $pais = Pais::findOrFail($idinterno);
            } else {
                $pais = new Pais();
            }

            $pais->fill($data);
    
            $pais->save();
            if ($commitTransaction) {
                static::commitTran();
            }
            return $pais;
        } catch (\Exception $e) {
            static::rollbackTran();
            throw $e;
        }
    }
}
