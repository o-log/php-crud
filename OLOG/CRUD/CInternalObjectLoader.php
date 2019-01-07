<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG\CRUD;

class CInternalObjectLoader
{

    public static function createAndLoadObject($model_class_name, $obj_id)
    {
        if(!is_a($model_class_name, \OLOG\Model\ActiveRecordInterface::class, true)){
            throw new \Exception();
        }

        return $model_class_name::factory($obj_id);
    }
}
