<?php

namespace OLOG\CRUD;

use OLOG\Assert;
use OLOG\Operations;
use OLOG\Sanitize;

class CRUDTableWidgetDelete implements InterfaceCRUDTableWidget
{
    public function html($obj){
        Assert::assert($obj);
        
        $text = 'X';

        $o = '';
        $o .= '<form method="post" action="' . \OLOG\Url::getCurrentUrl() . '">';
        $o .= Operations::operationCodeHiddenField(CRUDTable::OPERATION_DELETE_MODEL);
        $o .='<input type="hidden" name="_class_name" value="' . Sanitize::sanitizeAttrValue(get_class($obj)) . '">';
        $o .='<input type="hidden" name="_id" value="' . Sanitize::sanitizeAttrValue(CRUDFieldsAccess::getObjId($obj)) . '">';

        $o .='<button class="btn btn-xs" type="submit" onclick="return window.confirm(\'Delete?\');">' . $text . '</button>';

        $o .='</form>';

        return $o;

    }
}