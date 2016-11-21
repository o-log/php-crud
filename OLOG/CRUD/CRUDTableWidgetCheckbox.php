<?php
namespace OLOG\CRUD;

class CRUDTableWidgetCheckbox implements InterfaceCRUDTableWidget
{
    protected $field_name;

    public function __construct($field_name) {
        $this->setFieldName($field_name);
    }

    /**
     * @return string
     */
    public function getFieldName()
    {
        return $this->field_name;
    }

    /**
     * @param string $field_name
     */
    public function setFieldName($field_name)
    {
        $this->field_name = $field_name;
    }

    public function html($obj) {
        if( CRUDFieldsAccess::getObjectFieldValue($obj, $this->getFieldName() )) {
            $html = '<span style ="text-decoration: none;" class="glyphicon glyphicon-check"></span>';
        } else {
            $html = '<span style ="text-decoration: none;" class="glyphicon glyphicon-unchecked"></span>';
        }

        return $html;
    }
}
