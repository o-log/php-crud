<?php

namespace OLOG\CRUD;

class CRUDElementVerticalFormRow
{
    const KEY_FORM_ROW_FIELD_NAME = 'FORM_ROW_FIELD_NAME';
    const KEY_FORM_ROW_TITLE = 'FORM_ROW_TITLE';

    static public function generateHtml($widget_html, $field_title){
        //$field_name = CRUDConfigReader::getRequiredSubkey($element_config_arr, self::KEY_FORM_ROW_FIELD_NAME);
        //$field_title = CRUDConfigReader::getOptionalSubkey($element_config_arr, self::KEY_FORM_ROW_TITLE, $field_name);

        $html = '';

        $html .= '<div class="form-group">';
        $html .= '<div class="col-sm-12" style="font-weight: bold;">' . $field_title . '</div>';

        $html .= '<div class="col-sm-12">';
        //$widget_config_arr = CRUDConfigReader::getRequiredSubkey($element_config_arr, 'WIDGET');
        $html .= $widget_html;

        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }
}