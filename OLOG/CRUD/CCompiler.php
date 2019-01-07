<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG\CRUD;

class CCompiler {
    public static function compile($str, array $data)
    {
        if (self::is_closure($str)){
            return $str($data['this']);
        }

        if (array_key_exists('this', $data)) {
            $_this = $data['this'];
            if (CInternalFieldsAccess::objectHasProperty($_this, $str)){
                return CInternalFieldsAccess::getObjectFieldValue($_this, $str);
            }
        }

        return $str;
    }

    public static function is_closure($t) {
        return is_object($t) && ($t instanceof \Closure);
    }
}
