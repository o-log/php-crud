<?php

namespace OLOG\CRUD;

class ObjectLoader
{

    public static function createAndLoadObject($model_class_name, $obj_id)
    {
        // TODO: use interfaceFactory if implemented!

        \OLOG\Model\Helper::exceptionIfClassNotImplementsInterface($model_class_name, 'OLOG\Model\InterfaceLoad');

        $obj = new $model_class_name;
        \OLOG\Helpers::assert($obj->load($obj_id));

        return $obj;
    }
}