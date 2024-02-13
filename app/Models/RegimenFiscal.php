<?php

namespace App\Models;

/**
 
 */
class RegimenFiscal extends Model
{
    //
    protected $table = 'regimenfiscalsat';

    protected $casts = [
    ];

    protected $fillable = [
        'id', 
        'claveregimenfiscal', 
        'descripcion'
    ];


    /**
     * Método encargado de crear o actualizar una empresa
     *
     * @param array $data Datos que desea crear o actualizar en el empresa
     * @param string $idinterno
     *  Llave única del registro de la empresa que desea editar
     * @param boolean $commitTransaction Parámetro que indica si deseas terminar la transacción
     *                                      o no, por default es <b>true</b>
     * @return RegimenFiscal
     * @throws \Exception
     */
    public static function createOrUpdate($data, $idinterno = '', $commitTransaction = true)
    {
        static::beginTran();

        try {
            $regimenfiscal = null;
            if (trim($idinterno) !== '') {
                $regimenfiscal = RegimenFiscal::findOrFail($idinterno);
            } else {
                $regimenfiscal = new RegimenFiscal();
            }

            $regimenfiscal->fill($data);
    
            $regimenfiscal->save();
            if ($commitTransaction) {
                static::commitTran();
            }
            return $regimenfiscal;
        } catch (\Exception $e) {
            static::rollbackTran();
            throw $e;
        }
    }
}
