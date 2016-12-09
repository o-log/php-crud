<?php

namespace OLOG\CRUD;

use OLOG\Assert;
use OLOG\GETAccess;

class CRUDTableFilterNotInInvisible implements InterfaceCRUDTableFilterInvisible
{
    protected $field_name;
    protected $filter_value;

    public function getValue(){
        return $this->filter_value;
    }
    public function setValue($val){
        $this->filter_value=$val;
    }

    public function getHtml(){
        $html = '';
        return $html;
    }

    /**
     * Возвращает пару из sql-условия и массива значений плейсхолдеров. Массив значений может быть пустой если плейсхолдеры не нужны.
     * @return array
     */
    public function sqlConditionAndPlaceholderValue()
    {
        $filter_value_arr = $this->getValue();
        if(!count($filter_value_arr)){
            return['', []];
        }

        $placeholder_values_arr=[];
        $column_name = $this->getFieldName();

        $in_arr=[];
        foreach ($filter_value_arr  as $val){
            $in_arr[]='?';
            $placeholder_values_arr[]=$val;
        }
        $where = $column_name." not IN(".implode(',',$in_arr).") ";

        return [$where, $placeholder_values_arr];
    }

    public function __construct($field_name,  $value){
        $this->setFieldName($field_name);
        $this->setValue($value);
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return '';
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