<?php

namespace OLOG\CRUD;

use OLOG\Assert;

class CRUDFieldsAccess
{
    static public function getObjId($obj)
    {
        Assert::assert($obj);
        
        $obj_class_name = get_class($obj);
        $obj_id_field_name = CRUDFieldsAccess::getIdFieldName($obj_class_name);
        return CRUDFieldsAccess::getObjectFieldValue($obj, $obj_id_field_name);
        
    }
    
    public static function getIdFieldName($model_class_name)
    {
        if (defined($model_class_name . '::DB_ID_FIELD_NAME')) {
            return $model_class_name::DB_ID_FIELD_NAME;
        } else {
            return 'id';
        }
    }

    public static function getObjectFieldValue($obj, $field_name)
    {
        $obj_class_name = get_class($obj);

        $reflect = new \ReflectionClass($obj_class_name);
        $field_prop_obj = null;

        foreach ($reflect->getProperties() as $prop_obj) {
            if ($prop_obj->getName() == $field_name) {
                $field_prop_obj = $prop_obj;
            }
        }

        \OLOG\Assert::assert($field_prop_obj, 'Field "' . $field_name . '" not found in object. Object class: "' . $obj_class_name . '"');

        $field_prop_obj->setAccessible(true);
        return $field_prop_obj->getValue($obj);
    }

    /**
     * @param $obj
     * @param $values_arr
     * @param array $null_fields_arr список полей объекта, в которые надо внести NULL
     * @return mixed
     */
    public static function setObjectFieldsFromArray($obj, $values_arr, $null_fields_arr = [])
    {
        $reflect = new \ReflectionClass($obj);

        foreach ($values_arr as $key => $value) {
            $property_obj = $reflect->getProperty($key);
            $property_obj->setAccessible(true);
            $property_obj->setValue($obj, $value);
        }

        foreach ($null_fields_arr as $key => $value) {
            $property_obj = $reflect->getProperty($key);
            $property_obj->setAccessible(true);
            $property_obj->setValue($obj, null);
        }

        return $obj;
    }
}