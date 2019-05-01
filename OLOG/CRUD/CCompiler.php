<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG\CRUD;

class CCompiler {
    public static function fieldValueOrCallableResult($fieldname_or_callable, $obj)
    {
        if (self::isClosure($fieldname_or_callable)){
            return $fieldname_or_callable($obj);
        }

        if (CInternalFieldsAccess::objectHasProperty($obj, $fieldname_or_callable)){
            return CInternalFieldsAccess::getObjectFieldValue($obj, $fieldname_or_callable);
        }

        return $fieldname_or_callable;
    }

    public static function isClosure($t) {
        return is_object($t) && ($t instanceof \Closure);
    }
}
