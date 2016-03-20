<?php

namespace OLOG\CRUD;

class CRUDWidgetTextarea
{
    // TODO: проблема: передачи значения поля в явном виде:
    // - во первых дубрирует в создавалке формы обращение к объекту для каждого поля
    // - во вторых требует или публикации полей, или создания геттеров
    // использовать в виджетах какой-то общий объект всей формы? как?
    public static function html($field_name, $field_value = '')
    {
        return '<textarea name="' . Sanitize::sanitizeAttrValue($field_name) . '" class="form-control" rows="5">' . Sanitize::sanitizeTagContent($field_value) . '</textarea>';
    }

}