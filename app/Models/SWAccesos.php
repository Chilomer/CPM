<?php

namespace App\Models;

/**
 
 */
class SWAccesos extends Model
{
    //
    protected $table = 'swaccesos';
    protected $primaryKey = 'id';

    protected $casts = [
    ];

    protected $fillable = [
        'id',
        'idempresa',
        'usuario',
        'contrasena',
        'idsw'
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
