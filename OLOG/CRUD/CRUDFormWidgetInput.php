<?php

namespace OLOG\CRUD;

use OLOG\Sanitize;

class CRUDFormWidgetInput implements InterfaceCRUDFormWidget
{
    protected $field_name;
    protected $show_null_checkbox;
    protected $is_required;

    /**
     * @return mixed
     */
    public function getIsRequired()
    {
        return $this->is_required;
    }

    /**
     * @param mixed $is_required
     */
    public function setIsRequired($is_required)
    {
        $this->is_required = $is_required;
    }

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


    public function __construct($field_name, $show_null_checkbox = false, $is_required = false)
    {
        $this->setFieldName($field_name);
        $this->setShowNullCheckbox($show_null_checkbox);
        $this->setIsRequired($is_required);
    }

    public function html($obj)
    {
        $field_name = $this->getFieldName();
        $field_value = CRUDFieldsAccess::getObjectFieldValue($obj, $field_name);

        $is_null_checked = '';
        if (is_null($field_value)) {
            $is_null_checked = ' checked ';
        }

        $html = '';

        $input_cols = $this->getShowNullCheckbox() ? '10' : '12';

        $html .= '<div class="row">';
        $html .= '<div class="col-sm-' . $input_cols . '">';

        $is_required_str = '';
        $required_class = '';
        if ($this->is_required){
            $is_required_str = ' required ';
            $required_class = ' has-warning ';
        }

        $html .= '<input name="' . Sanitize::sanitizeAttrValue($field_name) . '" ' . $is_required_str . ' class="form-control ' . $required_class . '" value="' . Sanitize::sanitizeAttrValue($field_value) . '"/>';
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