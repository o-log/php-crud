<?php

namespace OLOG\CRUD;

use OLOG\Sanitize;

class CRUDFormWidgetCheckbox implements InterfaceCRUDFormWidget
{
    protected $field_name;

    public function __construct($field_name)
    {
        $this->setFieldName($field_name);
    }

    public function html($obj)
    {
        $field_name = $this->getFieldName();
        $field_value = CRUDFieldsAccess::getObjectFieldValue($obj, $field_name);

        //return '<textarea name="' . Sanitize::sanitizeAttrValue($field_name) . '" class="form-control" rows="1">' . Sanitize::sanitizeTagContent($field_value) . '</textarea>';
        $checked_str = '';
        if ($field_value){
            $checked_str = ' checked ';
        }

        return '<input name="' . Sanitize::sanitizeAttrValue($field_name) . '" type="checkbox" ' . $checked_str. ' >';
        //return '<input name="' . Sanitize::sanitizeAttrValue($field_name) . '" class="form-control" value="' . Sanitize::sanitizeAttrValue($field_value) . '"/>';
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
/*
static public function formCheckbox($field_name, $checked){
    ob_start();

    echo '<div class="checkbox">';
    echo '<label>';

    echo '</label>';
    echo '</div>';

    return ob_get_clean();
}
*/