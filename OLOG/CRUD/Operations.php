<?php

namespace OLOG\CRUD;

// TODO должно уехать в роутер?

class Operations
{
    const FIELD_NAME_OPERATION_CODE = 'FIELD_NAME_OPERATION_CODE';

    public static function operationCodeHiddenField($operation_code){
        return '<input type="hidden" name="' . self::FIELD_NAME_OPERATION_CODE . '" value="' . Sanitize::sanitizeAttrValue($operation_code) . '">';
    }

    public static function matchOperation($operation_code, callable $callback_arr)
    {
        if (isset($_POST[self::FIELD_NAME_OPERATION_CODE])) {
            if ($_POST[self::FIELD_NAME_OPERATION_CODE] == $operation_code) {
                call_user_func($callback_arr);
            }
        }
    }
}