<?php

namespace OLOG\CRUD;

use OLOG\Assert;
use OLOG\GETAccess;
use OLOG\Sanitize;

class CRUDTableFilterEqualOptions implements InterfaceCRUDTableFilter2
{
    protected $title;
    protected $field_name;
    protected $filter_iniq_id;
    protected $initial_is_enabled;
    protected $initial_value;
    protected $options_arr;
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

    /**
     * @return mixed
     */
    public function getInitialValue()
    {
        return $this->initial_value;
    }

    /**
     * @param mixed $initial_value
     */
    public function setInitialValue($initial_value)
    {
        $this->initial_value = $initial_value;
    }

    /**
     * @return mixed
     */
    public function getInitialIsEnabled()
    {
        return $this->initial_is_enabled;
    }

    /**
     * @param mixed $initial_is_enabled
     */
    public function setInitialIsEnabled($initial_is_enabled)
    {
        $this->initial_is_enabled = $initial_is_enabled;
    }

    public function getValueFromForm(){
        $value = GETAccess::getOptionalGetValue($this->getFilterIniqId(), null);

        // if no value passed in request
        if (is_null($value)){
            return $this->getInitialValue();
        }

        $is_null = GETAccess::getOptionalGetValue($this->getFilterIniqId() . '___is_null'); // TODO: remove scalar

        if ($is_null != ''){
            $value = null;
        }

        return $value;
    }

    public function enabledCheckboxName(){
        return $this->getFilterIniqId() . '___enabled';
    }

    public function widgetHtmlForValue($field_value, $input_name = null)
    {
        $html = '';

        $options_arr = $this->getOptionsArr();


        if($this->getShowNullCheckbox()) {
        }

        $html .= '<select onchange="$(this).closest(\'form\').submit();" id="' . $input_name . '" name="' . $input_name . '" class="form-control">';
        foreach($options_arr as $value => $title)
        {
            $selected_html_attr = '';
            if ($field_value == $value) {
                $selected_html_attr = ' selected';
            }

            $html .= '<option value="' .  $value . '"' . $selected_html_attr . '>' . $title . '</option>';
        }
        $html .= '</select>';

        if($this->getShowNullCheckbox()) {
            $html .= '<div class="input-group-addon">';
            $is_null_checked = is_null($field_value) ? ' checked ' : '';

            $html .= '<input type = "checkbox" value="1" name="' . Sanitize::sanitizeAttrValue($input_name) . '___is_null" ' . $is_null_checked . ' /> null';
            $html .= '</div>';
        }

        return $html;

    }


    public function getHtml(){
        $html = '';

        //$input_name = self::filterFormFieldName($table_index_on_page, $filter_index);
        $input_name = $this->getFilterIniqId();

        //$html .= '<div class="row"><div class="col-md-9">';
        $html .= '<div class="input-group">';

        $html .= $this->widgetHtmlForValue($this->getValueFromForm(), $input_name);

        //$html .= '</div><div class="col-md-3>">';

        $enabled_checked = $this->getInitialIsEnabled() ? ' checked ' : '';
        $html .= '<div class="input-group-addon">';
        $html .= '<label><input title="Filter active" onchange="f' . $input_name . '_enabledclick(this);" type="checkbox" id="' . $this->enabledCheckboxName() . '" name="' . $this->enabledCheckboxName() . '" ' . $enabled_checked . ' value="1"></label>';
        $html .= '</div>';

        $html .= '</div>';

        $html .= '
        <script>
        function f' . $input_name . '_enabledclick(checkbox_element){
            $("#' . $input_name . '").prop("disabled", !$(checkbox_element).prop("checked"));
            $(checkbox_element).closest("form").submit();
        }
        </script>
        ';

        //$html .= '</div></div>';

        return $html;
    }

    public function isEnabled(){
        $is_enabled_from_form = GETAccess::getOptionalGetValue($this->enabledCheckboxName(), $this->getInitialIsEnabled());

        if ($is_enabled_from_form != ''){
            return true;
        }

        return false;
    }

    /**
     * Возвращает пару из sql-условия и массива значений плейсхолдеров. Массив значений может быть пустой если плейсхолдеры не нужны.
     * @return array
     */
    public function sqlConditionAndPlaceholderValue()
    {
        $where = '';
        $placeholder_values_arr = [];

        $is_enabled = $this->isEnabled();

        if (!$is_enabled){
            return [$where, $placeholder_values_arr];
        }


        $value = $this->getValueFromForm();

        $column_name = $this->getFieldName();
        $column_name = preg_replace("/[^a-zA-Z0-9_]+/", "", $column_name);

        if (is_null($value)) {
            $where .= ' ' . $column_name . ' is null ';
        } else {
            $where .= ' ' . $column_name . ' = ? ';
            $placeholder_values_arr[] = $value;
        }

        return [$where, $placeholder_values_arr];
    }

    public function __construct($filter_uniq_id, $title, $field_name, $options_arr, $initial_is_enabled, $initial_value, $show_null_checkbox){
        $this->setFilterIniqId($filter_uniq_id);
        $this->setTitle($title);
        $this->setFieldName($field_name);
        $this->setOptionsArr($options_arr);
        $this->setInitialIsEnabled($initial_is_enabled);
        $this->setInitialValue($initial_value);
        $this->setShowNullCheckbox($show_null_checkbox);
    }

    /**
     * @return mixed
     */
    public function getFilterIniqId()
    {
        return $this->filter_iniq_id;
    }

    /**
     * @param mixed $filter_iniq_id
     */
    public function setFilterIniqId($filter_iniq_id)
    {
        $this->filter_iniq_id = $filter_iniq_id;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
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