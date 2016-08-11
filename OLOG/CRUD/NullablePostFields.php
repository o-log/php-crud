<?php

namespace OLOG\CRUD;

use OLOG\POSTAccess;
use OLOG\Sanitize;

class NullablePostFields
{
    static public function hiddenFieldHtml($field_name, $field_value){
        $is_null_value = '';
        if (is_null($field_value)) {
            $is_null_value = '1';
        }

        $html = '';
        $html .= '<input type="hidden" name="' . Sanitize::sanitizeAttrValue($field_name) . '" value="' . Sanitize::sanitizeAttrValue($field_value) . '"/>';
        $html .= '<input type="hidden" name="' . Sanitize::sanitizeAttrValue($field_name) . '___is_null" value="' . Sanitize::sanitizeAttrValue($is_null_value) . '"/>';

        return $html;
    }

    static public function optionalFieldValue($field_name){
        $field_value = POSTAccess::getOptionalPostValue($field_name);

        // чтение возможных NULL
        if (POSTAccess::getOptionalPostValue($field_name . "___is_null", '') == "1") {
            $field_value = null;
        }

        return $field_value;
    }
}