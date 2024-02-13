<?php

namespace App\Models;

/**
 
 */
class Software extends Model
{
    //
    protected $table = 'softwareexterno';

    protected $casts = [
        'id' => 'string'
    ];

    protected $fillable = [
        'nombresoftware', 
        'contactoprogramacion', 
        'emailcontactoprogramacion',
        'telefono',
        'cp',
        'timbracp',
        'publicar',
        'paginaweb',
        'emailpublicitario',
        'rfc',
        'nombrefiscal',
        'login',
        'contrasena'
    ];


    /**
     * Método encargado de crear o actualizar una empresa
     *
     * @param array $data Datos que desea crear o actualizar en el empresa
     * @param string $idinterno
     *  Llave única del registro de la empresa que desea editar
     * @param boolean $commitTransaction Parámetro que indica si deseas terminar la transacción
     *                                      o no, por default es <b>true</b>
     * @return Software
     * @throws \Exception
     */
    public static function createOrUpdate($data, $idinterno = '', $commitTransaction = true)
    {
        static::beginTran();

        try {
            $software = null;
            if (trim($idinterno) !== '') {
                $software = Software::findOrFail($idinterno);
            } else {
                $software = new Software();
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
