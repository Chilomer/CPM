<?php

namespace App\Models;

/**
 
 */
class Pedimentos extends Model
{
    //
    protected $table = 'pedimentos';

    protected $casts = [
    ];

    protected $fillable = [
        'idprecp',
        'idprecpdetalle', 
        'pedimento',
        'iddocumento',
        'idcpmercancia'
    ];


    /**
     * Método encargado de crear o actualizar una empresa
     *
     * @param array $data Datos que desea crear o actualizar en el empresa
     * @param string $idinterno
     *  Llave única del registro de la empresa que desea editar
     * @param boolean $commitTransaction Parámetro que indica si deseas terminar la transacción
     *                                      o no, por default es <b>true</b>
     * @return Pedimentos
     * @throws \Exception
     */
    public static function createOrUpdate($data, $idinterno = '', $commitTransaction = true)
    {
        static::beginTran();

        try {
            $pedimentos = null;
            if (trim($idinterno) !== '') {
                $pedimentos = Pedimentos::findOrFail($idinterno);
            } else {
                $pedimentos = new Pedimentos();
            }

            $pedimentos->fill($data);
    
            $pedimentos->save();
            if ($commitTransaction) {
                static::commitTran();
            }
            return $pedimentos;
        } catch (\Exception $e) {
            static::rollbackTran();
            throw $e;
        }
    }
}
