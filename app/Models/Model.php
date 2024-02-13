<?php

namespace App\Models;
use PDO;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model as ModelEloquent;

/**
 * Clase abstracta para heredar compartamiento y atributos comunes a los modelos de
 * la WebApi de Totall
 *
 * @property string idinterno
 */
abstract class Model extends ModelEloquent
{
    /**
     * 
     */
    public $incrementing = false;

    /**
     * Attributo que indica a Eloquent cual es el campo llave de la tabla
     * 
     * @var string
     */
    

    /**
     * Atributo que indica el tipo de dato del campo llave del Modelo
     * 
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Formato de las fechas
     * @var string
     */
    // protected $dateFormat = 'Y-m-d H:i:s.v';

    /**
     * Get the format for database stored dates.
     *
     * @return string
     */
    public function getDateFormat()
    {
        //$this->getConnection()->getConfig('driver')
        return in_array('dblib', PDO::getAvailableDrivers())
            ? 'Y-m-d H:i:s' : 'Y-m-d H:i:s.v';
    }

    public function fromDateTime($value)
    {
        return parent::fromDateTime($value);
        // return substr(parent::fromDateTime($value), 0, -3);
    }

    /**
     * Bandera que indica si el modelo usa los campos timestamp de Eloquent
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Atributo que permite asignar un idinterno desde otra entidad
     * @var string
     */
    public static $inheritId = null;

    /**
     * Atributo para indicar que existe una transacción abierta
     */
    public static $inTransaction = false;

    /**
     * Método encargado de iniciar una única transacción en la BD de Totall
     * NOTA: si ya existe una transacción abierto no vuelve a abrir otra
     */
    protected static function beginTran()
    {
        if (!static::$inTransaction) {
            DB::beginTransaction();
            static::$inTransaction = true;
        }
    }

    /**
     * Método encargado de terminar satisfactoriamente una transacción, si es que $inTransaction es
     * igual a 'true'
     */
    protected static function commitTran()
    {
        if (static::$inTransaction) {
            DB::commit();
            static::$inTransaction = false;
        }
    }

    /**
     * Método encargado de terminar y cancelar una transacción, si es que $inTransaction es
     * igual a 'true'
     */
    protected static function rollbackTran()
    {
        if (static::$inTransaction) {
            DB::rollBack();
            static::$inTransaction = false;
        }
    }

    /**
     * Boot function for using with User Events
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        self::creating(function ($data)
        {

            if (self::$inheritId !== null) {
                self::$inheritId = null;
            }
        });

        self::saving(function($data) {
            $data->fechamod = new Carbon();
        });
    }

    
}