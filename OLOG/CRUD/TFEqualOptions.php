<?php

namespace OLOG\CRUD;

use OLOG\REQUEST;
use OLOG\HTML;

class TFEqualOptions implements TFInterface
{
    protected $title;
    protected $field_name;
    protected $filter_iniq_id;
    protected $initial_is_enabled;
    protected $initial_value;
    protected $options_arr;
    protected $show_null_checkbox;

    // сообщает, нужно ли использовать значения из формы (включая отсутствующие в форме поля - для чекбоксов это означает false) или этот фильтр в форме не приходил и нужно использовать initial значения
    public function useValuesFromForm(){
        //$value = GETAccess::getOptionalGetValue($this->filterIsPassedInputName(), null);
        $value = REQUEST::optional($this->filterIsPassedInputName(), null);

        if (is_null($value)){
            return false;
        }

        return true;
    }

    public function nullCheckboxInputName(){
        return HTML::attr($this->getFilterIniqId() . '___is_null');
    }

    public function getValue(){
        if (!$this->useValuesFromForm()){
            return $this->getInitialValue();
        }

        //$value = GETAccess::getOptionalGetValue($this->getFilterIniqId());
        //$is_null = GETAccess::getOptionalGetValue($this->nullCheckboxInputName());
        $value = REQUEST::optional($this->getFilterIniqId());
        $is_null = REQUEST::optional($this->nullCheckboxInputName());

        if ($is_null != ''){
            $value = null;
        }

        return $value;
    }

    public function enabledCheckboxInputName(){
        return HTML::attr($this->getFilterIniqId() . '___enabled');
    }

    public function widgetHtmlForValue($field_value, $input_name)
    {
        $html = '';

        $html .= '<select onchange="f' . $input_name . '_selectchange(this);" id="' . $input_name . '" name="' . $input_name . '" class="form-control">';

        $options_arr = $this->getOptionsArr();
        foreach($options_arr as $value => $title){
            $html .= '<option value="' .  $value . '" ' . ($field_value == $value ? 'selected' : '') . '>' . $title . '</option>';
        }

        $html .= '</select>';

        if($this->getShowNullCheckbox()) {
            $html .= '<div class="input-group-addon">';

            $html .= '<input type="checkbox" onchange="f' . $input_name . '_nullchange(this);" value="1" id="' . $this->nullCheckboxInputName() . '" name="' . $this->nullCheckboxInputName() . '" ' . (is_null($field_value) ? ' checked ' : '') . ' /> null';
            $html .= '</div>';
        }

        return $html;
    }

    public function filterIsPassedInputName(){
        return $this->getFilterIniqId() . '___passed';
    }

    public function getHtml(){
        $html = '';

        $input_name = $this->getFilterIniqId();

        $html .= '<div class="input-group">';

        // отдельное поле, наличие которого сообщает что фильтр присутствует в форме (все другие поля могут отсутствовать когда фильтр например запрещен и т.п.)
        $html .= '<input type="hidden" name="' . $this->filterIsPassedInputName() . '" value="1">';

        $html .= $this->widgetHtmlForValue($this->getValue(), $input_name);

        $html .= '<div class="input-group-addon">';
        $html .= '<label>';
        $html .= '<input title="Filter active" onchange="f' . $input_name . '_enabledclick(this);" type="checkbox" id="' . $this->enabledCheckboxInputName() . '" name="' . $this->enabledCheckboxInputName() . '" ' . ($this->isEnabled() ? 'checked' : '') . ' value="1">';
        $html .= '</label>';
        $html .= '</div>';

        $html .= '</div>';

        $html .= '<script>

        function f' . $input_name . '_selectchange(select_element){
            $(select_element).closest("form").submit();
        }
        
        function f' . $input_name . '_nullchange(checkbox_element){
            f' . $input_name . '_updatedisabled();
            $(checkbox_element).closest("form").submit();
        }
        
        function f' . $input_name . '_enabledclick(checkbox_element){
            f' . $input_name . '_updatedisabled();
            $(checkbox_element).closest("form").submit();
        }
        
        function f' . $input_name . '_updatedisabled(){
            var enabled = $("#' . $this->enabledCheckboxInputName() . '").prop("checked");
            if (enabled){
                $("#' . $this->nullCheckboxInputName() . '").prop("disabled", false);
                if ($("#' . $this->nullCheckboxInputName() . '").length > 0){ // if widget has null checkbox
                    var is_null = $("#' . $this->nullCheckboxInputName() . '").prop("checked");
                    $("#' . $input_name . '").prop("disabled", is_null);
                } else {
                    $("#' . $input_name . '").prop("disabled", false);
                }
            } else {
                $("#' . $input_name . '").prop("disabled", true);
                $("#' . $this->nullCheckboxInputName() . '").prop("disabled", true);
            }
        }

        f' . $input_name . '_updatedisabled();
        
        </script>';

        return $html;
    }

    public function isEnabled(){
        if (!$this->useValuesFromForm()){
            return $this->getInitialIsEnabled();
        }

        //$is_enabled_from_form = GETAccess::getOptionalGetValue($this->enabledCheckboxInputName());
        $is_enabled_from_form = REQUEST::optional($this->enabledCheckboxInputName());

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
        if (!$this->isEnabled()){
            return ['', []];
        }

        $value = $this->getValue();
        //$sanitized_column_name = Sanitize::sanitizeSqlColumnName($this->getFieldName());
        $sanitized_column_name = preg_replace('@\W@', '_', $this->getFieldName());

        if (is_null($value)) {
            return [
                ' ' . $sanitized_column_name . ' is null ',
                []
            ];
        }

        return [
            ' ' . $sanitized_column_name . ' = ? ',
            [$value]
        ];
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
}