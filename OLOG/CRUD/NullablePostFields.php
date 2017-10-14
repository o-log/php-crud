<?php

namespace OLOG\CRUD;

use OLOG\POST;
use OLOG\HTML;

class NullablePostFields
{
    static public function hiddenFieldHtml($field_name, $field_value){
        $is_null_value = '';
        if (is_null($field_value)) {
            $is_null_value = '1';
        }

        $html = '';
        $html .= '<input type="hidden" name="' . HTML::attr($field_name) . '" value="' . HTML::attr($field_value) . '"/>';
        $html .= '<input type="hidden" name="' . HTML::attr($field_name) . '___is_null" value="' . HTML::attr($is_null_value) . '"/>';

        return $html;
    }

    static public function optionalFieldValue($field_name){
        $field_value = POST::optional($field_name);

        // чтение возможных NULL
        if (POST::optional($field_name . "___is_null", '') == "1") {
            $field_value = null;
        }

        return $field_value;
    }
}