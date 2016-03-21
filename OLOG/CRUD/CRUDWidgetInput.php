<?php

namespace OLOG\CRUD;

use OLOG\Sanitize;

class CRUDWidgetInput
{
    protected $field_name;

    public function __construct($field_name)
    {
        $this->setFieldName($field_name);
    }

    public function html($obj)
    {
        $field_name = $this->getFieldName();
        $field_value = FieldsAccess::getObjectFieldValue($obj, $field_name);

        return '<textarea name="' . Sanitize::sanitizeAttrValue($field_name) . '" class="form-control" rows="1">' . Sanitize::sanitizeTagContent($field_value) . '</textarea>';
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