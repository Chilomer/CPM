<?php

namespace App\Models;

/**
 
 */
class Empresa extends Model
{
    //
    protected $table = 'empresas';

    protected $casts = [
        'cancelado'                   => 'boolean',
        'id' => 'string'
    ];
    protected $primaryKey = "id";

    protected $fillable = [
        'regimenfiscal', 
        'nombrefiscal', 
        'nombrecomercial', 
        'representante', 
        'telefonoempresa', 
        'identificador',
        'contrasena',
        // factor 1 corresponde a presentacion 2 porque factor de la presentacion 1 siempre es 1
        'email', 
        'rfc',
        'calle',
        'numext',
        'numint',
        'colonia',
        'localidad',
        'municipio',
        'cp',
        'estado',
        'pais',
        'giro',
        'tipousuario',
        'tipoempresa',
        'fechaalta',
        'cancelado',
        'fechacancelado'
    ];


    /**
     * Método encargado de crear o actualizar una empresa
     *
     * @param array $data Datos que desea crear o actualizar en el empresa
     * @param string $idinterno
     *  Llave única del registro de la empresa que desea editar
     * @param boolean $commitTransaction Parámetro que indica si deseas terminar la transacción
     *                                      o no, por default es <b>true</b>
     * @return Empresa
     * @throws \Exception
     */
    public static function createOrUpdate($data, $idinterno = '', $commitTransaction = true)
    {
        static::beginTran();

        try {
            $empresa = null;
            if (trim($idinterno) !== '') {
                $empresa = Empresa::findOrFail($idinterno);
            } else {
                $empresa = new Empresa();
            }

            $empresa->fill($data);
    
            $empresa->save();
            if ($commitTransaction) {
                static::commitTran();
            }
            
            return $empresa;
        } catch (\Exception $e) {
            static::rollbackTran();
            throw $e;
        }
    }
}
