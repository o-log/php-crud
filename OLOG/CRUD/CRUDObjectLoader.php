<?php

namespace OLOG\CRUD;

class CRUDObjectLoader
{

    public static function createAndLoadObject($model_class_name, $obj_id)
    {
        if(!is_a($model_class_name, \OLOG\Model\ActiveRecordInterface::class, true)){
            throw new \Exception();
        }

        return $model_class_name::factory($obj_id);
    }
}