<?php

namespace App\Models;

/**
 
 */
class Personal extends Model
{
    //
    protected $table = 'personal';

    // protected $casts = [
    //     'cancelado'                   => 'boolean'
    // ];

    protected $fillable = [   
        'usuariomod',
        'idempresa',
        'email',
        'telefono',
        'tipousuario',
        'curp',
        'numlicencia',
        'registrofiscal',
        'paisresidencia',
        'nombre',
        'rfc',
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
        'tipofigura'
    ];


    /**
     * Método encargado de crear o actualizar una personal
     *
     * @param array $data Datos que desea crear o actualizar en el personal
     * @param string $idinterno
     *  Llave única del registro de la personal que desea editar
     * @param boolean $commitTransaction Parámetro que indica si deseas terminar la transacción
     *                                      o no, por default es <b>true</b>
     * @return Personal
     * @throws \Exception
     */
    public static function createOrUpdate($data, $idinterno = '', $commitTransaction = true)
    {
        static::beginTran();

        try {
            $personal = null;
            if (trim($idinterno) !== '') {
                $personal = Personal::findOrFail($idinterno);
            } else {
                $personal = new Personal();
            }

            $personal->fill($data);
    
            $personal->save();
            if ($commitTransaction) {
                static::commitTran();
            }
            
            return $personal;
        } catch (\Exception $e) {
            static::rollbackTran();
            throw $e;
        }
    }
}
