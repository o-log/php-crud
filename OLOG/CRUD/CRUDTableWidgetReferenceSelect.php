<?php

namespace OLOG\CRUD;

use OLOG\Assert;
use OLOG\Operations;
use OLOG\Sanitize;

class CRUDTableWidgetReferenceSelect implements InterfaceCRUDTableWidget
{
    public function html($obj){
        Assert::assert($obj);

        $o = '';
        $o .='<button class="btn btn-xs btn-default js-ajax-form-select" type="submit" data-id="' . Sanitize::sanitizeAttrValue(CRUDFieldsAccess::getObjId($obj)) . '">Выбор</button>';

        return $o;

    }
}