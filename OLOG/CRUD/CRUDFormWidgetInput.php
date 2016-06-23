<?php

namespace OLOG\CRUD;

use OLOG\Sanitize;

class CRUDFormWidgetInput implements InterfaceCRUDFormWidget
{
    protected $field_name;
    protected $show_null_checkbox;

    /**
     * @return mixed
     */
    public function getShowNullCheckbox()
    {
        return $this->show_null_checkbox;
    }

    /**
     * @param mixed $show_null_checkbox
     */
    public function setShowNullCheckbox($show_null_checkbox)
    {
        $this->show_null_checkbox = $show_null_checkbox;
    }


    public function __construct($field_name, $show_null_checkbox = false)
    {
        $this->setFieldName($field_name);
        $this->setShowNullCheckbox($show_null_checkbox);
    }

    public function html($obj)
    {
        $field_name = $this->getFieldName();
        $field_value = CRUDFieldsAccess::getObjectFieldValue($obj, $field_name);

        $is_null_checked = '';
        if (is_null($field_value)) {
            $is_null_checked = ' checked ';
        }

        //return '<textarea name="' . Sanitize::sanitizeAttrValue($field_name) . '" class="form-control" rows="1">' . Sanitize::sanitizeTagContent($field_value) . '</textarea>';
        $html = '';

        $input_cols = $this->getShowNullCheckbox() ? '10' : '12';

        $html .= '<div class="row">';
        $html .= '<div class="col-sm-' . $input_cols . '">';
        $html .= '<input name="' . Sanitize::sanitizeAttrValue($field_name) . '" class="form-control" value="' . Sanitize::sanitizeAttrValue($field_value) . '"/>';
        $html .= '</div>';

        if ($this->getShowNullCheckbox()) {
            $html .= '<div class="col-sm-2">';
            $html .= '<label class="form-control-static">';
            $html .= '<input type="checkbox" value="1" name="' . Sanitize::sanitizeAttrValue($field_name) . '___is_null" ' . $is_null_checked . ' /> NULL';
            $html .= '</label>';
            $html .= '</div>';
        }
        $html .= '</div>';

        return $html;
    }

    /**
     * @return mixed
     */
    public function getFieldName()
    {
        return $this->field_name;
    }

    /**
     * @param mixed $field_name
     */
    public function setFieldName($field_name)
    {
        $this->field_name = $field_name;
    }


}