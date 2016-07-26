<?php

namespace OLOG\CRUD;

class CRUDTableFilter implements InterfaceCRUDTableFilter
{
    const FILTER_IS_NULL = 'FILTER_IS_NULL';
    const FILTER_EQUAL = 'FILTER_EQUAL';
    const FILTER_LIKE = 'FILTER_LIKE';

    protected $field_name;
    protected $operation_code;
    protected $value;
    
    public function __construct($field_name, $operation_code, $value = null)
    {
        $this->setFieldName($field_name);
        $this->setOperationCode($operation_code);
        $this->setValue($value);
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
    public function getOperationCode()
    {
        return $this->operation_code;
    }

    /**
     * @param mixed $operation_code
     */
    public function setOperationCode($operation_code)
    {
        $this->operation_code = $operation_code;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
    
    
}