<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG\CRUD;

class TFInHidden implements TFHiddenInterface
{
    protected $field_name;
    protected $filter_value;

    public function __construct($field_name,  $value){
        $this->setFieldName($field_name);
        $this->setValue($value);
    }

    public function getValue(){
        return $this->filter_value;
    }
    public function setValue($val){
        $this->filter_value=$val;
    }

    /*
    public function getHtml(){
        $html = '';
        return $html;
    }
    */

    /**
     * Возвращает пару из sql-условия и массива значений плейсхолдеров. Массив значений может быть пустой если плейсхолдеры не нужны.
     * @return array
     */
    public function sqlConditionAndPlaceholderValue()
    {
        $filter_value_arr = $this->getValue();
        if(!count($filter_value_arr)){
            // если значений для фильтрации нет - выборка должна быть пустой, поэтому ставим невыполнимый фильтр
            return[' 1=2 ', []];
        }

        $placeholder_values_arr=[];
        $column_name = $this->getFieldName();

        $in_arr=[];
        foreach ($filter_value_arr  as $val){
            $in_arr[]='?';
            $placeholder_values_arr[]=$val;
        }
        $where = $column_name." IN(".implode(',',$in_arr).") ";

        return [$where, $placeholder_values_arr];
    }

    /**
     * @return mixed
     */
    /*
    public function getTitle()
    {
        return '';
    }
    */

    /**
     * @param mixed $title
     */
    /*
    public function setTitle($title)
    {
        $this->title = $title;
    }
    */

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
