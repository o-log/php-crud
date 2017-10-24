<?php

namespace OLOG\CRUD;

class TFNotNullInvisibleInterface implements TFInvisibleInterface
{
    protected $field_name;

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
        $column_name = $this->getFieldName();

        $where = $column_name . " is not null ";

        return [$where, []];
    }

    public function __construct($field_name){
        $this->setFieldName($field_name);
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