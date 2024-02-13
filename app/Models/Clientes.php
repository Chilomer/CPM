<?php

namespace App\Models;

/**
 
 */
class Clientes extends Model
{
    //
    protected $table = 'clientes';
    protected $primaryKey = 'id';

    protected $casts = [
    ];

    protected $fillable = [
        'id',
        'idempresa',
        'nombre',
        'nombrecomercial'
    ];

    public function direcciones()
    {
        return Direcciones::where('idcliente', $this->id)->get();
    }


    /**
     * Método encargado de crear o actualizar una empresa
     *
     * @param array $data Datos que desea crear o actualizar en el empresa
     * @param string $idinterno
     *  Llave única del registro de la empresa que desea editar
     * @param boolean $commitTransaction Parámetro que indica si deseas terminar la transacción
     *                                      o no, por default es <b>true</b>
     * @return Clientes
     * @throws \Exception
     */
    public static function createOrUpdate($data, $idinterno = '', $commitTransaction = true)
    {
        static::beginTran();

        try {
            $clientes = null;
            if (trim($idinterno) !== '') {
                $clientes = Clientes::findOrFail($idinterno);
            } else {
                $clientes = new Clientes();
            }

            $clientes->fill($data);
    
            $clientes->save();
            if ($commitTransaction) {
                static::commitTran();
            }
            return $clientes;
        } catch (\Exception $e) {
            static::rollbackTran();
            throw $e;
        }
    }
}
