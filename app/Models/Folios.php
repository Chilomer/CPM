<?php

namespace App\Models;

/**
 
 */
class Folios extends Model
{
    //
    protected $table = 'folios';

    protected $casts = [
    ];

    protected $fillable = [
        'idempresa',
        'foliostimbres', 
        'fechaultcompratimbres',
        'foliosprecp',
        'fechaultcompraprecp'
    ];


    /**
     * Método encargado de crear o actualizar una empresa
     *
     * @param array $data Datos que desea crear o actualizar en el empresa
     * @param string $idinterno
     *  Llave única del registro de la empresa que desea editar
     * @param boolean $commitTransaction Parámetro que indica si deseas terminar la transacción
     *                                      o no, por default es <b>true</b>
     * @return Folios
     * @throws \Exception
     */
    public static function createOrUpdate($data, $idinterno = '', $commitTransaction = true)
    {
        static::beginTran();

        try {
            $folios = null;
            if (trim($idinterno) !== '') {
                $folios = Folios::findOrFail($idinterno);
            } else {
                $folios = new Folios();
            }

            $folios->fill($data);
    
            $folios->save();
            if ($commitTransaction) {
                static::commitTran();
            }
            return $folios;
        } catch (\Exception $e) {
            static::rollbackTran();
            throw $e;
        }
    }
}
