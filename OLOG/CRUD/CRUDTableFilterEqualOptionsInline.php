<?php

namespace OLOG\CRUD;

use OLOG\Assert;
use OLOG\GETAccess;
use OLOG\Sanitize;

class CRUDTableFilterEqualOptionsInline implements InterfaceCRUDTableFilter2
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
        $value = GETAccess::getOptionalGetValue($this->filterIsPassedInputName(), null);

        if (is_null($value)){
            return false;
        }

        return true;
    }

    public function nullCheckboxInputName(){
        return Sanitize::sanitizeAttrValue($this->getFilterIniqId() . '___is_null');
    }

    public function getValue(){
        if (!$this->useValuesFromForm()){
            return $this->getInitialValue();
        }

        $value = GETAccess::getOptionalGetValue($this->getFilterIniqId());
        $is_null = GETAccess::getOptionalGetValue($this->nullCheckboxInputName());

        if ($is_null != ''){
            $value = null;
        }

        return $value;
    }

    public function enabledCheckboxInputName(){
        return Sanitize::sanitizeAttrValue($this->getFilterIniqId() . '___enabled');
    }

    public function filterIsPassedInputName(){
        return $this->getFilterIniqId() . '___passed';
    }

    public function getHtml(){
        $html = '';

        $input_name = $this->getFilterIniqId();

	    $html .= '<div class="js-filter">';
        // отдельное поле, наличие которого сообщает что фильтр присутствует в форме (все другие поля могут отсутствовать когда фильтр например запрещен и т.п.)
        $html .= '<input type="hidden" name="' . $this->filterIsPassedInputName() . '" value="1">';

	    $html .= '<input type="hidden" name="' . $this->enabledCheckboxInputName() . '" value="' . ($this->isEnabled() ? '1' : '') . '">';
	    $html .= '<input type="radio" value="" name="' . $input_name . '" id="' . $input_name . '_all" onchange="f' . $input_name . '_changeFiltres(this);" ' . ($this->isEnabled() ? '' : 'checked') . ' style="z-index: -11;position: absolute;opacity: 0;">';
	    $html .= '<label for="' . $input_name . '_all"><span class="btn btn-default ' . ($this->isEnabled() ? '' : 'active') . '">Все</span></label>';

	    $options_arr = $this->getOptionsArr();
	    foreach($options_arr as $value => $title){
		    $html .= '<input type="radio" value="' . $value . '" name="' . $input_name . '" id="' . $input_name . '_' . $value . '" onchange="f' . $input_name . '_changeFiltres(this);" style="z-index: -11;position: absolute;opacity: 0;">';
		    $html .= '<label for="' . $input_name . '_' . $value . '"><span class="btn btn-default">' . $title . '</span></label>';
	    }

	    if($this->getShowNullCheckbox()) {
		    $html .= '<input type="hidden" name="' . $this->nullCheckboxInputName() . '" value="' . (is_null($this->getValue()) ? '' : '1') . '">';
		    $html .= '<input type="radio" value="" name="' . $input_name . '" id="' . $input_name . '_is_null" onchange="f' . $input_name . '_changeFiltres(this);" style="z-index: -11;position: absolute;opacity: 0;">';
		    $html .= '<label for="' . $input_name . '_is_null"><span class="btn btn-default">Не указано</span></label>';
	    }
	    $html .= '</div>';

		ob_start(); ?>
        <script>
        function f<?= $input_name ?>_changeFiltres(select_element){
	        var $this = $(select_element);
	        var $form = $this.closest('form');
	        var $filter = $this.closest('.js-filter');

	        $filter.find('.btn').removeClass('active');
	        $filter.find('[for="' + $this.attr('id') + '"]').find('.btn').addClass('active');

	        if ($this.is('#<?= $input_name ?>_all')) {
		        $filter.find('[name="<?= $this->enabledCheckboxInputName() ?>"]').val('');
	        } else {
		        $filter.find('[name="<?= $this->enabledCheckboxInputName() ?>"]').val('1');
	        }

	        if ($this.is('#<?= $input_name ?>_is_null')) {
		        $filter.find('[name="<?= $this->nullCheckboxInputName() ?>"]').val('1');
	        } else {
		        $filter.find('[name="<?= $this->nullCheckboxInputName() ?>"]').val('');
	        }

	        $form.submit();
        }
        </script>
		<?php
	    $html .= ob_get_clean();

        return $html;
    }

    public function isEnabled(){
        if (!$this->useValuesFromForm()){
            return $this->getInitialIsEnabled();
        }

        $is_enabled_from_form = GETAccess::getOptionalGetValue($this->enabledCheckboxInputName());

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
        $sanitized_column_name = Sanitize::sanitizeSqlColumnName($this->getFieldName());

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