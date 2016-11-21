<?php

namespace OLOG\CRUD;

use OLOG\Operations;
use OLOG\Sanitize;

class CRUDTableWidgetCheckbox implements InterfaceCRUDTableWidget
{
    protected $value;
    protected $field_name;

    const FIELD_CLASS_NAME = '_class_name';
    const FIELD_OBJECT_ID = '_id';
    const FIELD_NAME = '_field_name';

    public function __construct($value, $field_name) {
        $this->setValue(intval($value));
        $this->setFieldName($field_name);
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
    public function setValue($value) {
        $this->value = $value;

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

    public function html($obj) {
        $value = CRUDCompiler::compile($this->getValue(), ['this' => $obj]);
        $checked = $value ?  'checked' : '';

        $html = '<form  id style="display: inline;" class="form-inline" method="post"  action="' . \OLOG\Url::getCurrentUrl() . '">';
        $html .='<input type="hidden" name="' . self::FIELD_CLASS_NAME . '" value="' . Sanitize::sanitizeAttrValue(get_class($obj)) . '">';
        $html .='<input type="hidden" name="' . self::FIELD_OBJECT_ID . '" value="' . Sanitize::sanitizeAttrValue(CRUDFieldsAccess::getObjId($obj)) . '">';
        $html .='<input type="hidden" name="' . self::FIELD_NAME . '" value="' . Sanitize::sanitizeAttrValue($this->field_name) . '">';
        if($checked) {
            $html .= '<button style ="text-decoration: none;" class="glyphicon glyphicon-check btn btn-link btn-xs" onClick="submit()" ></button>';
        } else {
            $html .= '<button style ="text-decoration: none;" class="glyphicon glyphicon-unchecked btn btn-link btn-xs" onClick="submit()"></button>';
        }

        $html .= Operations::operationCodeHiddenField(CRUDTable::OPERATION_CHECK_FIELD);
        $html .= '</form>';
        return $html;
    }
}
