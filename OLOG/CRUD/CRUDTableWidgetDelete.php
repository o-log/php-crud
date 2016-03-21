<?php

namespace OLOG\CRUD;

use OLOG\Assert;
use OLOG\Sanitize;

class CRUDTableWidgetDelete
{
    public function html($obj){
        Assert::assert($obj);
        
        $text = 'X';

        $o = '';
        $o .= '<form method="post" action="' . \OLOG\Url::getCurrentUrl() . '">';
        $o .= Operations::operationCodeHiddenField(CRUDTable::OPERATION_DELETE_MODEL);
        $o .='<input type="hidden" name="_class_name" value="' . Sanitize::sanitizeAttrValue(get_class($obj)) . '">';
        $o .='<input type="hidden" name="_id" value="' . Sanitize::sanitizeAttrValue(FieldsAccess::getObjId($obj)) . '">';

        $o .='<button type="submit" onclick="return window.confirm(\'Delete?\');">' . $text . '</button>';

        $o .='</form>';

        return $o;

    }
}