<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG\CRUD;

class TWCheckbox implements TWInterface
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
        if( CInternalFieldsAccess::getObjectFieldValue($obj, $this->getFieldName() )) {
            $html = '<span style ="text-decoration: none;" class="fa fa-check"></span>';
        } else {
            $html = '<span style ="text-decoration: none;" class="fa fa-unchecked"></span>';
        }

        return $html;
    }
}
