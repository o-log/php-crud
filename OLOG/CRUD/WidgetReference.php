<?php

namespace OLOG\CRUD;

class WidgetReference
{
    public static function render($widget_config_arr, $obj){
        $text = CRUDConfigReader::getRequiredSubkey($widget_config_arr, 'TEXT');
        $text = self::compile($text, ['this' => $obj]);

        $o = Sanitize::sanitizeTagContent($text);

        return $o;
    }
}