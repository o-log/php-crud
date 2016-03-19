<?php

namespace OLOG\CRUD;

class CRUDElementVerticalFormRow
{
    const KEY_FORM_ROW_FIELD_NAME = 'FORM_ROW_FIELD_NAME';
    const KEY_FORM_ROW_TITLE = 'FORM_ROW_TITLE';

    static public function render($element_config_arr, $obj){
        $field_name = CRUDConfigReader::getRequiredSubkey($element_config_arr, self::KEY_FORM_ROW_FIELD_NAME);
        $field_title = CRUDConfigReader::getOptionalSubkey($element_config_arr, self::KEY_FORM_ROW_TITLE, $field_name);

        echo '<div class="form-group">';
        echo '<div class="col-sm-12" style="font-weight: bold;">' . $field_title . '</div>';

        echo '<div class="col-sm-12">';
        $widget_config_arr = CRUDConfigReader::getRequiredSubkey($element_config_arr, 'WIDGET');
        echo \OLOG\CRUD\CRUDWidgets::renderEditorFieldWithWidget($widget_config_arr, $field_name, $obj);

        echo '</div>';
        echo '</div>';
    }
}