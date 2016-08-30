<?php

namespace OLOG\CRUD;

use OLOG\Assert;
use OLOG\GETAccess;

class CRUDTableFilterEqual implements InterfaceCRUDTableFilter2
{
    protected $title;
    protected $field_name;
    protected $filter_iniq_id;
    protected $widget_obj;

    public function getValueFromForm(){
        $value = GETAccess::getOptionalGetValue($this->getFilterIniqId());

        $is_null = GETAccess::getOptionalGetValue($this->getFilterIniqId() . '___is_null'); // TODO: remove scalar

        if ($is_null != ''){
            $value = null;
        }

        return $value;
    }

    public function enabledCheckboxName(){
        return $this->getFilterIniqId() . '___enabled';
    }

    public function getHtml(){
        $html = '';

        //$input_name = self::filterFormFieldName($table_index_on_page, $filter_index);
        $input_name = $this->getFilterIniqId();

        /** @var InterfaceCRUDFormWidget $widget_obj */
        $widget_obj = $this->getWidgetObj();
        Assert::assert($widget_obj);

        $html .= $widget_obj->htmlForValue($this->getValueFromForm(), $input_name);

        $html .= '<label><input type="checkbox" name="' . $this->enabledCheckboxName() . '" value="1"> enabled</label>';

        return $html;
    }

    public function isEnabled(){
        $is_enabled_from_form = GETAccess::getOptionalGetValue($this->enabledCheckboxName());

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

    public function __construct($filter_uniq_id, $title, $field_name, $widget_obj){
        $this->setFilterIniqId($filter_uniq_id);
        $this->setTitle($title);
        $this->setFieldName($field_name);
        $this->setWidgetObj($widget_obj);
    }

    /**
     * @return mixed
     */
    public function getWidgetObj()
    {
        return $this->widget_obj;
    }

    /**
     * @param mixed $widget_obj
     */
    public function setWidgetObj($widget_obj)
    {
        $this->widget_obj = $widget_obj;
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