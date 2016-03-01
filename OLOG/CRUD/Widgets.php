<?php

namespace OLOG\CRUD;


class Widgets {
    const WIDGET_CHECKBOX = 'WIDGET_CHECKBOX';

    public static function renderEditorFieldWithWidget($field_name, $obj)
    {
        $widget_name = '';
        // TODO
        //$widget_name = self::getFieldWidgetName($field_name, $obj);

        $field_value = \OLOG\CRUD\FieldsAccess::getObjectFieldValue($obj, $field_name);

        /* TODO
        if (is_callable($widget_name)){

            $widget_options = self::getWidgetSettings($field_name, $obj);

            return call_user_func_array($widget_name, array($field_name, $field_value, $widget_options));
        }
        */

        switch($widget_name){
            /*
            case self::WIDGET_CHECKBOX:
                $o = self::widgetCheckbox($field_name, $field_value);
                break;
            case 'options':
                $options_arr = self::getFieldWidgetOptionsArr($field_name, $obj);
                $o = self::widgetOptions($field_name, $field_value, $options_arr);
                break;
            */
            default:
                $o = self::widgetInput($field_name, $field_value);
        }

        return $o;

    }

    /*
    public static function renderListFieldWithWidget($field_name, $obj, $field_value = '')
    {
        $widget_name = self::getListWidgetName($field_name, $obj);

        if (!$field_value) {
            $field_value = \OLOG\CRUD\Helpers::getObjectFieldValue($obj, $field_name);
        }

        if ($widget_name) {
            \OLOG\Helpers::assert(is_callable($widget_name));
            $widget_options = self::getWidgetSettings($field_name, $obj);

            return call_user_func_array($widget_name, array($field_name, $field_value, $widget_options));
        }

        return $field_value;

    }
    */

    /*
    public static function getFieldWidgetName($field_name, $obj)
    {
        $crud_editor_fields_arr = \OLOG\CRUD\Helpers::getCrudEditorFieldsArrForObj($obj);

        if(!$crud_editor_fields_arr){
            return '';
        }

        if(!array_key_exists($field_name, $crud_editor_fields_arr)){
            return '';
        }

        if(!array_key_exists('widget', $crud_editor_fields_arr[$field_name])){
            return '';
        }

        return $crud_editor_fields_arr[$field_name]['widget'];

    }
    */

    /*
    public static function getListWidgetName($field_name, $obj)
    {
        $crud_editor_fields_arr = \OLOG\CRUD\Helpers::getCrudEditorFieldsArrForObj($obj);

        if(!$crud_editor_fields_arr){
            return '';
        }

        if(!array_key_exists($field_name, $crud_editor_fields_arr)){
            return '';
        }

        if(!array_key_exists('list_widget', $crud_editor_fields_arr[$field_name])){
            return '';
        }

        return $crud_editor_fields_arr[$field_name]['list_widget'];

    }
    */

    /*
    public static function getWidgetSettings($field_name, $obj)
    {
        $crud_editor_fields_arr = \OLOG\CRUD\Helpers::getCrudEditorFieldsArrForObj($obj);

        if(!$crud_editor_fields_arr){
            return array();
        }

        if(!array_key_exists($field_name, $crud_editor_fields_arr)){
            return array();
        }

        if(!array_key_exists('widget_settings', $crud_editor_fields_arr[$field_name])){
            return array();
        }

        return $crud_editor_fields_arr[$field_name]['widget_settings'];

    }
    */

    /*
    public static function getFieldWidgetOptionsArr($field_name, $obj)
    {
        $crud_editor_fields_arr = \OLOG\CRUD\Helpers::getCrudEditorFieldsArrForObj($obj);

        if (!$crud_editor_fields_arr){
            return array();
        }

        if (!array_key_exists($field_name, $crud_editor_fields_arr)){
            return array();
        }

        if (!array_key_exists('options_arr', $crud_editor_fields_arr[$field_name])){
            return array();
        }

        return $crud_editor_fields_arr[$field_name]['options_arr'];
    }
    */

    public static function widgetInput($field_name, $field_value)
    {
        return '<textarea name="' . Sanitize::sanitizeAttrValue($field_name) . '" class="form-control" rows="1">' . Sanitize::sanitizeTagContent($field_value) . '</textarea>';
    }

    /*
    public static function widgetCheckbox($field_name, $field_value)
    {

        $checked_str = '';

        if($field_value){
            $checked_str = ' checked';
        }

        // после будет скрыто и попадет в POST только в том случае, если checkbox будет unchecked
        $hidden_field_for_unchecked_state = '<input type="hidden" name="' . $field_name . '" value="0">';

        $visible_checkbox = '<input type="checkbox" id="' . $field_name . '"
                               name="' . $field_name . '"
                               value="1"
                               ' . $checked_str . '>';

        return $hidden_field_for_unchecked_state . $visible_checkbox;

    }
    */

    public static function widgetOptions($field_name, $field_value, $options_arr)
    {
        $options = '<option></option>';

        foreach($options_arr as $value => $title)
        {
            $selected_html_attr = '';
            if ($field_value == $value) {
                $selected_html_attr = ' selected';
            }

            $options .= '<option value="' .  $value . '"' . $selected_html_attr . '>' . $title . '</option>';
        }

        return '<select name="' . $field_name . '" class="form-control">' . $options . '</select>';
    }
}