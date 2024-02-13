<?php

namespace App\Models;

/**
 
 */
class Polizas extends Model
{
    //
    protected $table = 'polizas';

    protected $casts = [
        'id' => 'string'
    ];

    protected $fillable = [
        'idempresa', 
        'idaseguradora',
        'tipopoliza',
        'poliza',
        'vencimiento',
        'primaseguro'
    ];

    public function aseguradora()
    {
        $aseguradora = Aseguradoras::where('id', $this->idaseguradora)->first();
        return $aseguradora->aseguradora;
    }


    /**
     * Método encargado de crear o actualizar una empresa
     *
     * @param array $data Datos que desea crear o actualizar en el empresa
     * @param string $idinterno
     *  Llave única del registro de la empresa que desea editar
     * @param boolean $commitTransaction Parámetro que indica si deseas terminar la transacción
     *                                      o no, por default es <b>true</b>
     * @return Polizas
     * @throws \Exception
     */
    public static function createOrUpdate($data, $idinterno = '', $commitTransaction = true)
    {
        static::beginTran();

        try {
            $software = null;
            if (trim($idinterno) !== '') {
                $software = Polizas::findOrFail($idinterno);
            } else {
                $software = new Polizas();
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
