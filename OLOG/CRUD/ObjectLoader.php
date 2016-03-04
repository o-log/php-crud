<?php

namespace OLOG\CRUD;

class ObjectLoader
{

    public static function createAndLoadObject($model_class_name, $obj_id)
    {
        \OLOG\Model\Helper::exceptionIfClassNotImplementsInterface($model_class_name, \OLOG\Model\InterfaceFactory::class);

        return $model_class_name::factory($obj_id);
    }
}