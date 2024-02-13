<?php

namespace App\Models;

/**
 
 */
class Vehiculos extends Model
{
    //
    protected $table = 'vehiculos';
    protected $primaryKey = 'id';

    protected $casts = [
    ];

    protected $fillable = [
        'id',
        'idempresa',
        'tipovehiculo',
        'descripcion',
        'clave',
        'placas',
        'capacidad',
        'ano',
        'tipopermisosct',
        'numpermiso',
        'configautotransportefed',
        'vehiculopropio',
        'idpropietario',
        'subtiporemolque',
        'idpolizarespcivil',

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
     * @return Vehiculos
     * @throws \Exception
     */
    public static function createOrUpdate($data, $idinterno = '', $commitTransaction = true)
    {
        static::beginTran();

        try {
            $vehiculos = null;
            if (trim($idinterno) !== '') {
                $vehiculos = Vehiculos::findOrFail($idinterno);
            } else {
                $vehiculos = new Vehiculos();
            }

            $vehiculos->fill($data);
    
            $vehiculos->save();
            if ($commitTransaction) {
                static::commitTran();
            }
            return $vehiculos;
        } catch (\Exception $e) {
            static::rollbackTran();
            throw $e;
        }
    }
}
