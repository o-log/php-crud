<?php
namespace OLOG\CRUD;

class CRUDTableWidgetCheckbox implements InterfaceCRUDTableWidget
{
    protected $field_name;

    const FIELD_CLASS_NAME = '_class_name';
    const FIELD_OBJECT_ID = '_id';
    const FIELD_NAME = '_field_name';

    public function __construct($field_name) {
        $this->setFieldName($field_name);
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
        $checked = CRUDFieldsAccess::getObjectFieldValue($obj, $this->getFieldName() ) ?  'checked' : '';
        if($checked) {
            $html = '<button style ="text-decoration: none;" class="glyphicon glyphicon-check btn btn-link btn-xs"></button>';
        } else {
            $html = '<button style ="text-decoration: none;" class="glyphicon glyphicon-unchecked btn btn-link btn-xs"></button>';
        }

        return $html;
    }
}
