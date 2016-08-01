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

        $input_cols = $this->getShowNullCheckbox() ? '10' : '12';
        $options = '<div class="col-sm-' . $input_cols . '">';
        $options .= '<option></option>';

        $options_arr = $this->getOptionsArr();

        foreach($options_arr as $value => $title)
        {
            $selected_html_attr = '';
            if ($field_value == $value) {
                $selected_html_attr = ' selected';
            }

            $options .= '<option value="' .  $value . '"' . $selected_html_attr . '>' . $title . '</option>';
        }
        $options .= '</div>';

        $is_null_checked = '';
        if(is_null($field_value))
        {
            $is_null_checked = ' checked ';
        }

        //return '<select name="' . $field_name . '" class="form-control">' . $options . '</select>';
        if($this->getShowNullCheckbox())
        {
            $options .= '<div class="col-sm-2">
                    <label class="form-control-static">
                        <input type = "checkbox" value = "1" name = "' . Sanitize::sanitizeAttrValue($field_name) . '___is_null" ' . $is_null_checked . ' /> null
                    </label >
                </div>';
        }

        $is_required_str = '';
        if ($this->is_required){
            $is_required_str = ' required ';
        }

        return '<select name="' . $field_name . '" class="form-control" ' . $is_required_str . '>' . $options . '</select>';
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