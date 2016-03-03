<?php

namespace OLOG\CRUD;


class Widgets {
    const WIDGET_CHECKBOX = 'WIDGET_CHECKBOX';

    static public function renderListWidget($widget_config_arr, $row_obj){
        $widget_type = CRUDConfigReader::getRequiredSubkey($widget_config_arr, 'WIDGET_TYPE');

        switch ($widget_type){
            case 'TEXT':
                return self::widgetText($widget_config_arr, $row_obj);

            case 'TEXT_WITH_LINK':
                return self::widgetTextWithLink($widget_config_arr, $row_obj);

            default:
                throw new \Exception('unknown list widget: ' . $widget_type);
        }
    }

    public static function compile($str, array $data){

        // TODO: clean and finish

        $matches = [];
        while (preg_match('@{([^}{]+)}@', $str, $matches)){
            $magic = $matches[1];
            $magic_replacement = 'UNKNOWN_MAGIC';

            $magic_matches = [];
            if (preg_match('@^(\w+)\->(\w+)$@', $magic, $magic_matches)){
                $obj_name_in_data = $magic_matches[1];
                $obj_field_name = $magic_matches[2];

                \OLOG\Assert::assert($data[$obj_name_in_data]);

                $magic_replacement = FieldsAccess::getObjectFieldValue($data[$obj_name_in_data], $obj_field_name);
            }

            if (preg_match('@^([\w\\\\]+)\.(\w+)->(\w+)$@', $magic, $magic_matches)){
                $class_name = $magic_matches[1];
                $obj_id = $magic_matches[2];
                $obj_field_name = $magic_matches[3];

                $obj = ObjectLoader::createAndLoadObject($class_name, $obj_id);
                $magic_replacement = FieldsAccess::getObjectFieldValue($obj, $obj_field_name);
            }

            $str = preg_replace('@{([^}{]+)}@', $magic_replacement, $str);
        }

        return $str;
    }

    public static function widgetText($widget_config_arr, $obj){
        $text = CRUDConfigReader::getRequiredSubkey($widget_config_arr, 'TEXT');
        $text = self::compile($text, ['this' => $obj]);

        $o = Sanitize::sanitizeTagContent($text);

        return $o;
    }

    public static function widgetTextWithLink($widget_config_arr, $obj){
        $url = CRUDConfigReader::getRequiredSubkey($widget_config_arr, 'LINK_URL');
        $url = self::compile($url, ['this' => $obj]);

        $text = CRUDConfigReader::getRequiredSubkey($widget_config_arr, 'TEXT');
        $text = self::compile($text, ['this' => $obj]);

        if (trim($text) == ''){
            $text = '#EMPTY#';
        }

        $o = '<a href="' . Sanitize::sanitizeUrl($url) . '">' . Sanitize::sanitizeTagContent($text) . '</a>';

        return $o;
    }

    public static function renderEditorFieldWithWidget($widget_config_arr, $field_name, $obj = null)
    {
        $widget_name = CRUDConfigReader::getRequiredSubkey($widget_config_arr, 'WIDGET_TYPE');
        // TODO
        //$widget_name = self::getFieldWidgetName($field_name, $obj);

        $field_value = '';
        if ($obj) {
            $field_value = \OLOG\CRUD\FieldsAccess::getObjectFieldValue($obj, $field_name);
        }

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
            case 'WIDGET_TEXTAREA':
                return self::widgetTextarea($field_name, $field_value);
            case 'WIDGET_INPUT':
                return self::widgetInput($field_name, $field_value);
            default:
                throw new \Exception('unknown widget type: ' . $widget_name);
        }
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

    public static function widgetTextarea($field_name, $field_value)
    {
        return '<textarea name="' . Sanitize::sanitizeAttrValue($field_name) . '" class="form-control" rows="5">' . Sanitize::sanitizeTagContent($field_value) . '</textarea>';
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