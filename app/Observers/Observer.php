<?php

namespace App\Observers;

use Validator;

/**
 * Clase base para nuestros Observers
 */
class Observer
{
    /**
     * Arreglo que indica las reglas de validación de los diferentes campos del modelo
     * que extienda de Model, la idea es que se le de valor en cada clase que se desee que se valide
     * estos rules
     */
    public $rules = [];

    /**
     * Attributo para guardar los $rules originales y poder volver a procesar los rules
     * fixRules
     */
    private $origin_rules = [];

    private $errors;
    
    /**
     * Método encargado de validar datos recibidos contra la reglas establecidas para este Observer
     */
    public function validate($model)
    {
        $hasRules = is_array($this->rules) && count($this->rules) > 0;
        
        if ($hasRules)
        {
            $data = $this->getModelAttributes($model);
            
            $this->fixRules($data);

            // make a new validator object
            $v = Validator::make($data, $this->rules);

            // Regresar los rules a como estaban originalmente
            $this->rules = array_merge(array(), $this->origin_rules);
    
            // check for failure
            if ($v->fails())
            {
                // set errors and return false
                $this->errors = $v->errors()->all();
                // TODO: Meter manejo de errores
                throw new \Illuminate\Validation\ValidationException($v);
            }
    
            // validation pass
            return true;
        }
        else
        {
            return true;
        }
    }

    /**
     * Método encargado de obtener los errores despues de la validación
     */
    public function errors()
    {
        return $this->errors;
    }

    /**
     * Obtener los attributos del modelo que esta observando esta clase
     * 
     * @param array $model 
     * 
     * @return array
     */
    public function getModelAttributes($model)
    {
        $attributes = $model->getModel()->getAttributes();
        foreach ($attributes as $key => $value)
        {
            if (in_array($key, $model->getDates())) {
                $attributes[$key] = $value;
                continue;
            }
            $attributes[$key] = $model->getModel()->getAttributeValue($key);
        }
        return $attributes;
    }

    /**
     * Método encargado de meter comportamiento en tiempo de ejecución a los rules de validación
     * 
     * @param array $data
     */
    private function fixRules($data)
    {
        $this->origin_rules = array_merge(array(), $this->rules);
        
        // Por cada rule en este Observer
        foreach ($this->rules as $key => $rule)
        {
            if ($rule !== null && !is_array($rule)) {
                $this->fixIdInternoUniqueID($rule, $data);
                $this->rules[$key] = $rule;
            }
        }
    }

    /**
     * Método encargado de injectar el idinterno en las Rules unique del Observer
     * 
     * @param string $rule
     * @param array $data
     */
    private function fixIdInternoUniqueID(&$rule, $data)
    {
        if (strpos($rule, '{idinterno}') !== false && strpos($rule, 'unique:') !== false) {
            $idinterno = isset($data['idinterno']) ? $data['idinterno'] : null;
            if ($idinterno !== null && $idinterno !== '')
            {
                $rule = str_replace('{idinterno}', ",$idinterno,idinterno", $rule);
            }
            else
            {
                $rule = str_replace('{idinterno}', '', $rule);
            }
        }
    }
}



