<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG\CRUD;

use OLOG\HTML;
use OLOG\REQUEST;

class TFEqualSelectInline implements TFInterface
{
    protected $title;
    protected $field_name;
    protected $filter_iniq_id;
    protected $initial_is_enabled;
    protected $initial_value;
    protected $options_arr;
    protected $show_null_checkbox;
    protected $btn_all_text;

    // сообщает, нужно ли использовать значения из формы (включая отсутствующие в форме поля - для чекбоксов это означает false) или этот фильтр в форме не приходил и нужно использовать initial значения
    public function useValuesFromForm(){
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

    public function filterIsPassedInputName(){
        return $this->getFilterIniqId() . '___passed';
    }

    public function getHtml(){
        echo '<span class="js-filter">';
        $input_name = $this->getFilterIniqId();
        /**
         * отдельное поле, наличие которого сообщает что фильтр присутствует в форме
         * (все другие поля могут отсутствовать когда фильтр например запрещен и т.п.)
         */
        echo '<input type="hidden" name="' . $this->filterIsPassedInputName() . '" value="1">';
        echo '<input type="hidden" name="' . $this->enabledCheckboxInputName() . '" value="' . ($this->isEnabled() ? '1' : '') . '">';
        echo '<input type="hidden" name="' . $input_name . '" value="' . ($this->isEnabled() ? $this->getValue() : '') . '">';

        if($this->getShowNullCheckbox()) {
            echo '<input type="hidden" name="' . $this->nullCheckboxInputName() . '" value="' . ((is_null($this->getValue()) && ($this->isEnabled())) ? '1' : '') . '">';
        }

        $options_arr = $this->getOptionsArr();

        echo '<select class="custom-select custom-select-sm" onchange="f' . $input_name . '_changeFiltres(this);">';

        echo '<option ' . ($this->isEnabled() ? '' : 'selected') . '>' . $this->getBtnAllText() . '</option>';

        foreach($options_arr as $value => $title){
            echo '<option data-value="' . $value . '" data-enabled="1" ' . (($this->isEnabled() && ($this->getValue() == $value)) ? 'selected' : '') . '>' . $title . '</option>';
        }

        if($this->getShowNullCheckbox()) {
            echo '<option data-isnull="1" data-enabled="1" ' . ((is_null($this->getValue()) && ($this->isEnabled())) ? 'selected' : '') . '>Не указано</option>';
        }

        echo '</select>';

        echo '</span>';

        $input_name = $this->getFilterIniqId();
        ob_start(); ?>
        <script>
            function f<?= $input_name ?>_changeFiltres(select_element){
                var $option = $(select_element).find(":selected");

                var $select_element = $(select_element);
                var $form = $select_element.closest('form');
                var $filter = $select_element.closest('.js-filter');

                //$filter.find('.btn').removeClass('active');
                //$this.addClass('active');

                var enabled = $option.data('enabled') || '';
                var value = $option.data('value') || '';
                var isnull = $option.data('isnull') || '';

                $filter.find('[name="<?= $this->enabledCheckboxInputName() ?>"]').val(enabled);
                $filter.find('[name="<?= $input_name ?>"]').val(value);
                $filter.find('[name="<?= $this->nullCheckboxInputName() ?>"]').val(isnull);

                $form.submit();
            }
        </script>
        <?php
        $script = ob_get_clean();

        return $script;
    }

    public function isEnabled(){
        if (!$this->useValuesFromForm()){
            return $this->getInitialIsEnabled();
        }

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

    public function __construct($filter_uniq_id, $title, $field_name, $options_arr, $initial_is_enabled, $initial_value, $show_null_checkbox, $btn_all_text = 'Все'){
        $this->setFilterIniqId($filter_uniq_id);
        $this->setTitle($title);
        $this->setFieldName($field_name);
        $this->setOptionsArr($options_arr);
        $this->setInitialIsEnabled($initial_is_enabled);
        $this->setInitialValue($initial_value);
        $this->setShowNullCheckbox($show_null_checkbox);
        $this->setBtnAllText($btn_all_text);
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

    /**
     * @return mixed
     */
    public function getBtnAllText()
    {
        return $this->btn_all_text;
    }

    /**
     * @param mixed $btn_all_text
     */
    public function setBtnAllText($btn_all_text)
    {
        $this->btn_all_text = $btn_all_text;
    }


}
