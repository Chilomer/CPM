<?php

namespace App\Models;

/**
 
 */
class Articulos extends Model
{
    //
    protected $table = 'articulos';

    protected $casts = [
        'id' => 'string'
    ];

    protected $fillable = [
        'idempresa',
        'clave',
        'isbn',
        'iddepartamento',
        'descripcion',
        'presentacion',
        'claveunidadsat',
        'claveprodservsat',
        'pesokgs',
        'materialpeligroso',
        'clavematpeligroso',
        'claveembalaje',
        'esconcepto',
        'dimensiones',
        'moneda',
        'unidadpeso',
        'pesobruto',
        'pesoneto',
        'pesotara'
        
        
    ];


    /**
     * Método encargado de crear o actualizar una empresa
     *
     * @param array $data Datos que desea crear o actualizar en el empresa
     * @param string $idinterno
     *  Llave única del registro de la empresa que desea editar
     * @param boolean $commitTransaction Parámetro que indica si deseas terminar la transacción
     *                                      o no, por default es <b>true</b>
     * @return Articulos
     * @throws \Exception
     */
    public static function createOrUpdate($data, $idinterno = '', $commitTransaction = true)
    {
        static::beginTran();

        try {
            $articulos = null;
            if (trim($idinterno) !== '') {
                $articulos = Articulos::findOrFail($idinterno);
            } else {
                $articulos = new Articulos();
            }

            $articulos->fill($data);
    
            $articulos->save();
            if ($commitTransaction) {
                static::commitTran();
            }
            return $articulos;
        } catch (\Exception $e) {
            static::rollbackTran();
            throw $e;
        }
    }
}
