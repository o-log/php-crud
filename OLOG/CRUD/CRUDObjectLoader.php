<?php

namespace OLOG\CRUD;

class CRUDObjectLoader
{

    public static function createAndLoadObject($model_class_name, $obj_id)
    {
        \OLOG\CheckClassInterfaces::exceptionIfClassNotImplementsInterface($model_class_name, \OLOG\Model\InterfaceFactory::class);

        return $model_class_name::factory($obj_id);
    }
}