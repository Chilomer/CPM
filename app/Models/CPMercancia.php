<?php

namespace App\Models;

/**
 
 */
class CPMercancia extends Model
{
    //
    protected $table = 'cpmercancia';

    // protected $casts = [
    //     'cancelado'                   => 'boolean'
    // ];

    protected $fillable = [   
        'usuariomod',
        'idprecp',
        'iddocumento',
        'idempresa',
        'idarticulo',
        'descripcion',
        'bienestransp',
        'unidad',
        'claveunidad',
        'materialpeligroso',
        'clavematpeligroso',
        'claveembalaje',
        'cantidad',
        'dimensiones',
        'pesoenkg',
        'valormercancia',
        'moneda',
        'fraccionarancelaria',
        'uuidcomercioext',
        'clavearticulo'    
    ];


    /**
     * Método encargado de crear o actualizar una precpdetalle
     *
     * @param array $data Datos que desea crear o actualizar en el precpdetalle
     * @param string $idinterno
     *  Llave única del registro de la precpdetalle que desea editar
     * @param boolean $commitTransaction Parámetro que indica si deseas terminar la transacción
     *                                      o no, por default es <b>true</b>
     * @return CPMercancia
     * @throws \Exception
     */
    public static function createOrUpdate($data, $idinterno = '', $commitTransaction = true)
    {
        static::beginTran();

        try {
            $precpdetalle = null;
            if (trim($idinterno) !== '') {
                $precpdetalle = CPMercancia::findOrFail($idinterno);
            } else {
                $precpdetalle = new CPMercancia();
            }

            $precpdetalle->fill($data);
    
            $precpdetalle->save();
            if ($commitTransaction) {
                static::commitTran();
            }
            
            return $precpdetalle;
        } catch (\Exception $e) {
            static::rollbackTran();
            throw $e;
        }
    }
}
