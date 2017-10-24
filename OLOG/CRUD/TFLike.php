<?php

namespace OLOG\CRUD;

use OLOG\REQUEST;

class TFLike implements TFInterface
{
    protected $title;
    protected $field_name;
    protected $filter_iniq_id;

    public function getValueFromForm(){
        $value = REQUEST::optional($this->getFilterIniqId());

        // LIKE filter doesn't use nulls

        return $value;
    }

    //public function enabledCheckboxName(){
        //return $this->getFilterIniqId() . '___enabled';
    //}

    public function getHtml(){
        $html = '';

        $input_name = $this->getFilterIniqId();

        //$html .= '<div class="row"><div class="col-md-10">';

        $html .= '<input onkeyup="$(this).closest(\'form\').submit();" class="form-control" name="' . $input_name . '"/>';

        //$html .= '</div><div class="col-md-2>">';

        //$html .= '<label><input type="checkbox" name="' . $this->enabledCheckboxName() . '" value="1"> enabled</label>';

        //$html .= '</div></div>';

        return $html;
    }

    /*
    public function isEnabled(){
        $is_enabled_from_form = GETAccess::getOptionalGetValue($this->enabledCheckboxName());

        if ($is_enabled_from_form != ''){
            return true;
        }

        return false;
    }
    */

    /**
     * Возвращает пару из sql-условия и массива значений плейсхолдеров. Массив значений может быть пустой если плейсхолдеры не нужны.
     * @return array
     */
    public function sqlConditionAndPlaceholderValue()
    {
        $where = '';
        $placeholder_values_arr = [];

        // для этого виджета галка включения не выводится: если в поле пустая строка - он игрорируется

        /*
        $is_enabled = $this->isEnabled();

        if (!$is_enabled){
            return [$where, $placeholder_values_arr];
        }
        */

        $value = $this->getValueFromForm();

        $column_name = $this->getFieldName();
        $column_name = preg_replace("/[^a-zA-Z0-9_]+/", "", $column_name);

        if ($value != '') {
            $where .= ' ' . $column_name . ' like ? ';
            $placeholder_values_arr[] = '%' . $value . '%';
        }

        return [$where, $placeholder_values_arr];
    }

    public function __construct($filter_uniq_id, $title, $field_name){
        $this->setFilterIniqId($filter_uniq_id);
        $this->setTitle($title);
        $this->setFieldName($field_name);
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