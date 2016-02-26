<?php

namespace OLOG\CRUD;

class FieldsAccess
{
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

        \OLOG\Helpers::assert($field_prop_obj);

        $field_prop_obj->setAccessible(true);
        return $field_prop_obj->getValue($obj);
    }

    public static function setObjectFieldsFromArray($obj, $values_arr)
    {
        $reflect = new \ReflectionClass($obj);

        foreach ($values_arr as $key => $value) {
            $property_obj = $reflect->getProperty($key);
            $property_obj->setAccessible(true);
            $property_obj->setValue($obj, $value);
        }

        return $obj;
    }

    /*
    static public function getTitleForField($model_class_name, $field_name)
    {
        $title = $field_name;

        // TODO
        //if (property_exists($model_class_name, 'crud_field_titles_arr')) {
        //    $crud_field_titles_arr = $model_class_name::$crud_field_titles_arr;
        //    if (array_key_exists($field_name, $crud_field_titles_arr)) {
        //        $title = $crud_field_titles_arr[$field_name];
        //    }
        //}

        return $title;
    }
    */

}