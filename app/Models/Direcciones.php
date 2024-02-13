<?php

namespace App\Models;

/**
 
 */
class Direcciones extends Model
{
    //
    protected $table = 'direcciones';

    // protected $casts = [
    //     'cancelado'                   => 'boolean'
    // ];

    protected $fillable = [   
        'usuariomod',
        'idempresa',
        'idcliente',
        'nombre',
        'descripcion',
        'rfc',
        'numregidtrib',//*poner en tabla
        'residenciafiscal',//*poner en tabla
        'calle',
        'numext',
        'numint',
        'cp',
        'colonia',
        'c_colonia',
        'estado',
        'c_estado',
        'clave_entfed',
        'municipio',
        'c_municipio',
        'localidad',
        'c_localidad',
        'pais',
        'referencia'
    ];


    /**
     * Método encargado de crear o actualizar una direcciones
     *
     * @param array $data Datos que desea crear o actualizar en el direcciones
     * @param string $idinterno
     *  Llave única del registro de la direcciones que desea editar
     * @param boolean $commitTransaction Parámetro que indica si deseas terminar la transacción
     *                                      o no, por default es <b>true</b>
     * @return Direcciones
     * @throws \Exception
     */
    public static function createOrUpdate($data, $idinterno = '', $commitTransaction = true)
    {
        static::beginTran();

        try {
            $direcciones = null;
            if (trim($idinterno) !== '') {
                $direcciones = Direcciones::findOrFail($idinterno);
            } else {
                $direcciones = new Direcciones();
            }

            $direcciones->fill($data);
    
            $direcciones->save();
            if ($commitTransaction) {
                static::commitTran();
            }
            
            return $direcciones;
        } catch (\Exception $e) {
            static::rollbackTran();
            throw $e;
        }
    }
}
