<?php

namespace OLOG\CRUD;

class CRUDFormRow
{
    static public function html($widget_html, $field_title){
        $html = '';
        
        $required = false;
        // TODO
        //$required = \Sportbox\CRUD\Helpers::isRequiredField($model_class_name, $prop_obj->getName());

        $editor_description = '';
        // TODO
        //$editor_description = \Sportbox\CRUD\Helpers::getDescriptionForField($model_class_name, $prop_obj->getName());

        //$editor_context_obj = CRUDController::getEditorContext();

        //$field_name = $element_config_arr[self::KEY_FORM_ROW_FIELD_NAME];
        //$field_name = CRUDConfigReader::getRequiredSubkey($element_config_arr, self::KEY_FORM_ROW_FIELD_NAME);
        //$field_title = CRUDConfigReader::getOptionalSubkey($element_config_arr, self::KEY_FORM_ROW_TITLE, $field_name);

        $html .= '<div class="form-group ' . ($required ? 'required' : '') . '">';
        $html .= '<label class="col-sm-4 text-right control-label">' . $field_title . '</label>';

        $html .= '<div class="col-sm-8">';
        //$widget_config_arr = CRUDConfigReader::getRequiredSubkey($element_config_arr, 'WIDGET');
        //echo \OLOG\CRUD\CRUDWidgets::renderEditorFieldWithWidget($widget_config_arr, $field_name, $obj);
        $html .= $widget_html;

        if ($editor_description) {
            $html .= '<span class="help-block">' . $editor_description . '</span>';
        }

        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }

}