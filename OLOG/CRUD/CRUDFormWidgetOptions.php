<?php

namespace OLOG\CRUD;

use OLOG\Sanitize;

class CRUDFormWidgetOptions implements InterfaceCRUDFormWidget
{
    protected $field_name;
    protected $options_arr;
    protected $show_null_checkbox;
    protected $is_required;

    public function __construct($field_name, $options_arr, $show_null_checkbox = false, $is_required = false)
    {
        $this->setFieldName($field_name);
        $this->setOptionsArr($options_arr);
        $this->setShowNullCheckbox($show_null_checkbox);
        $this->setIsRequired($is_required);
    }

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

    public function html($obj)
    {
        $field_name = $this->getFieldName();
        $field_value = CRUDFieldsAccess::getObjectFieldValue($obj, $field_name);

        return $this->htmlForValue($field_value);
    }

    public function htmlForValue($field_value, $input_name = null)
    {
        $field_name = $this->getFieldName();
        $html = '';
        $options = '';

        if (is_null($input_name)){
            $input_name = $field_name;
        }

        $options_arr = $this->getOptionsArr();

        foreach($options_arr as $value => $title)
        {
            $selected_html_attr = '';
            if ($field_value == $value) {
                $selected_html_attr = ' selected';
            }

            $options .= '<option value="' .  $value . '"' . $selected_html_attr . '>' . $title . '</option>';
        }

        $is_null_checked = '';
        if(is_null($field_value))
        {
            $is_null_checked = ' checked ';
        }


        $is_required_str = '';
        if ($this->is_required){
            $is_required_str = ' required ';
        }

        $html .= '<div class="input-group">';
        $html .= '<select name="' . $input_name . '" class="form-control" ' . $is_required_str . '>' . $options . '</select>';

        if($this->getShowNullCheckbox()) {
            $html .= '<div class="input-group-addon">';
            $html .= '<input type = "checkbox" value="1" name="' . Sanitize::sanitizeAttrValue($input_name) . '___is_null" ' . $is_null_checked . ' /> null';
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

    /**
     * @return mixed
     */
    public function getOptionsArr()
    {
        return $this->options_arr;
    }

    /**
     * @param mixed $options_arr
     */
    public function setOptionsArr($options_arr)
    {
        $this->options_arr = $options_arr;
    }


}