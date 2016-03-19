<?php

namespace OLOG\CRUD;

class CRUDWidgetTextarea
{
    public static function getHtml($field_name, $field_value)
    {
        return '<textarea name="' . Sanitize::sanitizeAttrValue($field_name) . '" class="form-control" rows="5">' . Sanitize::sanitizeTagContent($field_value) . '</textarea>';
    }

}