<?php

namespace OLOG\CRUD;

use OLOG\Assert;
use OLOG\Operations;
use OLOG\Sanitize;

class CRUDTableWidgetReferenceSelect implements InterfaceCRUDTableWidget
{
    protected $title_field_name;

    public function __construct($title_field_name)
    {
        $this->setTitleFieldName($title_field_name);
    }

    public function html($obj){
        Assert::assert($obj);

        $title_field_name = $this->getTitleFieldName();

        $obj_title = CRUDFieldsAccess::getObjectFieldValue($obj, $title_field_name);

        $o = '';
        $o .='<button class="btn btn-xs btn-default js-ajax-form-select" type="submit" data-id="' . Sanitize::sanitizeAttrValue(CRUDFieldsAccess::getObjId($obj)) . '" data-title="' . Sanitize::sanitizeAttrValue($obj_title) . '">Выбор</button>';

        return $o;
    }

    /**
     * @return mixed
     */
    public function getTitleFieldName()
    {
        return $this->title_field_name;
    }

    /**
     * @param mixed $title_field_name
     */
    public function setTitleFieldName($title_field_name)
    {
        $this->title_field_name = $title_field_name;
    }


}