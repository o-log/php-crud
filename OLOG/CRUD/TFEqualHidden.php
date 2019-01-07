<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG\CRUD;

class TFEqualHidden implements TFHiddenInterface
{
    protected $title;
    protected $field_name;
    protected $filter_value;

    public function getValue(){
        return $this->filter_value;
    }
    public function setValue($val){
        $this->filter_value = $val;
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
        $filter_value = $this->getValue();
        $column_name = $this->getFieldName();
        $placeholder_values_arr = [];

        if (is_null($filter_value)){
            $where = $column_name . ' is null ';
        } else {
            $where = $column_name . ' = ? ';
            $placeholder_values_arr[] = $filter_value;
        }

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
