<?php

namespace OLOG\CRUD;

// TODO: вынести все виджеты отдельными классами
// TODO: rename this class to CRUDCompiler

class CRUDWidgets {
    //const WIDGET_CHECKBOX = 'WIDGET_CHECKBOX';
    //const WIDGET_TEXT_WITH_LINK = 'TEXT_WITH_LINK';

    /*
    static public function renderListWidget($widget_config_arr, $row_obj){
        $widget_type = CRUDConfigReader::getRequiredSubkey($widget_config_arr, 'WIDGET_TYPE');

        switch ($widget_type){
            case 'TEXT':
                return self::widgetText($widget_config_arr, $row_obj);

            case self::WIDGET_TEXT_WITH_LINK:
                return self::widgetTextWithLink($widget_config_arr, $row_obj);

            case 'DELETE':
                return self::widgetDelete($widget_config_arr, $row_obj);

            default:
                throw new \Exception('unknown list widget: ' . $widget_type);
        }
    }
    */

    /**
     * компиляция строки: разворачивание обращений к полям объектов
     * @param $str
     * @param array $data
     * @return mixed
     * @throws \Exception
     */
    public static function compile($str, array $data){

        // TODO: clean and finish

        $matches = [];

        // сначала подставляем значения в самых внутренних фигурных скобках, потом которые снаружи, и так пока все скобки не будут заменены
        // поддерживается два вида выражений:
        // - {obj->field} заменяется на значение поля field объекта obj. obj - это ключ массива data, т.е. здесь можно использовать такие строки, которые передаются сюда вызывающими функциями
        // -- обычно виджеты передают объект, который показывается в виджете, с именем this
        // - {class_name.id->field} заменяется на значение поля field объекта класса class_name с идентификатором id
        while (preg_match('@{([^}{]+)}@', $str, $matches)){
            $expression = $matches[1];
            $replacement = 'UNKNOWN_EXPRESSION';

            $magic_matches = [];
            if (preg_match('@^(\w+)\->(\w+)$@', $expression, $magic_matches)){
                $obj_key_in_data = $magic_matches[1];
                $obj_field_name = $magic_matches[2];

                \OLOG\Assert::assert($data[$obj_key_in_data]);
                $replacement = FieldsAccess::getObjectFieldValue($data[$obj_key_in_data], $obj_field_name);

                if (is_null($replacement)){
                    $replacement = 'NULL'; // TODO: review?
                }
            }

            if (preg_match('@^([\w\\\\]+)\.(\w+)->(\w+)$@', $expression, $magic_matches)){
                $class_name = $magic_matches[1];
                $obj_id = $magic_matches[2];
                $obj_field_name = $magic_matches[3];

                if ($obj_id != 'NULL') { // TODO: review?
                    $obj = ObjectLoader::createAndLoadObject($class_name, $obj_id);
                    $replacement = FieldsAccess::getObjectFieldValue($obj, $obj_field_name);
                } else {
                    $replacement = '';
                }
            }

            $str = preg_replace('@{([^}{]+)}@', $replacement, $str);
        }

        return $str;
    }

    /*
    public static function widgetText($widget_config_arr, $obj){
        $text = CRUDConfigReader::getRequiredSubkey($widget_config_arr, 'TEXT');
        $text = self::compile($text, ['this' => $obj]);

        $o = Sanitize::sanitizeTagContent($text);

        return $o;
    }
    */

    /*
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
    */

    /*
    public static function widgetDelete($widget_config_arr, $obj){
        $text = CRUDConfigReader::getRequiredSubkey($widget_config_arr, 'TEXT');
        $text = self::compile($text, ['this' => $obj]);

        if (trim($text) == ''){
            $text = '#EMPTY#';
        }

        $o = '';

        $obj_class_name = get_class($obj);
        $obj_id_field_name = FieldsAccess::getIdFieldName($obj_class_name);
        $obj_id = FieldsAccess::getObjectFieldValue($obj, $obj_id_field_name);

        $o .= '<form method="post" action="' . \OLOG\Url::getCurrentUrl() . '">';
        $o .= Operations::operationCodeHiddenField(CRUDList::OPERATION_DELETE_MODEL);
        $o .='<input type="hidden" name="_class_name" value="' . Sanitize::sanitizeAttrValue($obj_class_name) . '">';
        $o .='<input type="hidden" name="_id" value="' . Sanitize::sanitizeAttrValue($obj_id) . '">';

        $o .='<button type="submit" onclick="return window.confirm(\'Delete?\');">' . $text . '</button>';

        $o .='</form>';

        return $o;
    }
    */

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

    /*
'WIDGET' => [
'WIDGET_TYPE' => 'WIDGET_REFERENCE',
'REFERENCED_CLASS' => \CRUDDemo\Term::class,
'REFERENCED_CLASS_TITLE_FIELD' => 'title'
]
    */


    public static function widgetOptions($field_name, $field_value, $widget_config_arr)
    {
        $options = '<option></option>';

        $options_arr = $widget_config_arr['OPTIONS'];

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