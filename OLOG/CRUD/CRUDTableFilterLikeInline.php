<?php

namespace OLOG\CRUD;

use OLOG\Assert;
use OLOG\GETAccess;

class CRUDTableFilterLikeInline implements InterfaceCRUDTableFilter2
{
    protected $title;
    protected $field_name;
    protected $filter_iniq_id;

    public function getValueFromForm(){
        $value = GETAccess::getOptionalGetValue($this->getFilterIniqId());

        return $value;
    }

    public function getHtml(){
        $input_name = $this->getFilterIniqId();

	    $html = '';
        $html .= '<input onkeyup="$(this).closest(\'form\').submit();" name="' . $input_name . '"/>';

        return $html;
    }

    /**
     * Возвращает пару из sql-условия и массива значений плейсхолдеров. Массив значений может быть пустой если плейсхолдеры не нужны.
     * @return array
     */
    public function sqlConditionAndPlaceholderValue()
    {
        $where = '';
        $placeholder_values_arr = [];

        // для этого виджета галка включения не выводится: если в поле пустая строка - он игрорируется

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