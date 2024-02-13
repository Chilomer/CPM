<?php

namespace App\Models;

/**
 
 */
class PreCP extends Model
{
    //
    protected $table = 'precp';

    // protected $casts = [
    //     'cancelado'                   => 'boolean'
    // ];

    protected $fillable = [   
        'usuariomod',
        'nombrefiscal',
        'nombrecomercial',
        'rfc',
        'cp',
        'usocfdisat',
        'email',
        'idprecpimporta',
        'fecha',
        'idcliente',
        'fechaimportacion',
        'codigo',
        'numtotalmercancias',
        'transpinternac',
        'entradasalidamerc',
        'paisorigendestino',
        'viaentradasalida',
        'totaldistrec',
        'pesobrutototal',
        'unidadpeso',
        'idsoftwarecarga',
        'idsoftwaredescarga',
        'regimenfiscal'
    ];


    /**
     * Método encargado de crear o actualizar una precp
     *
     * @param array $data Datos que desea crear o actualizar en el precp
     * @param string $idinterno
     *  Llave única del registro de la precp que desea editar
     * @param boolean $commitTransaction Parámetro que indica si deseas terminar la transacción
     *                                      o no, por default es <b>true</b>
     * @return PreCP
     * @throws \Exception
     */
    public static function createOrUpdate($data, $idinterno = '', $commitTransaction = true)
    {
        static::beginTran();

        try {
            $precp = null;
            if (trim($idinterno) !== '') {
                $precp = PreCP::findOrFail($idinterno);
            } else {
                $precp = new PreCP();
            }

            $precp->fill($data);
    
            $precp->save();
            if ($commitTransaction) {
                static::commitTran();
            }
            
            return $precp;
        } catch (\Exception $e) {
            static::rollbackTran();
            throw $e;
        }
    }
}
