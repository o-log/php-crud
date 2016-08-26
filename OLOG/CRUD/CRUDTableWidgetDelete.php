<?php

namespace OLOG\CRUD;

use OLOG\Assert;
use OLOG\Operations;
use OLOG\Sanitize;

class CRUDTableWidgetDelete implements InterfaceCRUDTableWidget
{
    protected $redirect_after_delete_url;
    protected $button_text;
    protected $button_class_str;

    const FIELD_CLASS_NAME = '_class_name';
    const FIELD_OBJECT_ID = '_id';
    const FIELD_REDIRECT_AFTER_DELETE_URL = 'redirect_after_delete_url';

    public function __construct($button_text = '', $button_class_str = 'btn btn-xs btn-default glyphicon glyphicon-remove', $redirect_after_delete_url = ''){
        $this->button_class_str = $button_class_str;
        $this->button_text = $button_text;
        $this->redirect_after_delete_url = $redirect_after_delete_url;
    }

    public function html($obj){
        Assert::assert($obj);
        
        $o = '';
        $o .= '<form style="display: inline;" method="post" action="' . \OLOG\Url::getCurrentUrl() . '">';
        $o .= Operations::operationCodeHiddenField(CRUDTable::OPERATION_DELETE_MODEL);
        $o .='<input type="hidden" name="' . self::FIELD_CLASS_NAME . '" value="' . Sanitize::sanitizeAttrValue(get_class($obj)) . '">';
        $o .='<input type="hidden" name="' . self::FIELD_OBJECT_ID . '" value="' . Sanitize::sanitizeAttrValue(CRUDFieldsAccess::getObjId($obj)) . '">';

        if ($this->redirect_after_delete_url != ''){
            $o .='<input type="hidden" name="' . self::FIELD_REDIRECT_AFTER_DELETE_URL . '" value="' . Sanitize::sanitizeAttrValue($this->redirect_after_delete_url) . '">';
        }

        $o .='<button class="' . $this->button_class_str . '" type="submit" onclick="return window.confirm(\'Delete?\');">' . $this->button_text . '</button>';

        $o .='</form>';

        return $o;

    }
}