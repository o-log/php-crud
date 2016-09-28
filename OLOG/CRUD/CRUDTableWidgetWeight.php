<?php

namespace OLOG\CRUD;


use OLOG\Assert;
use OLOG\Model\InterfaceWeight;
use OLOG\Operations;
use OLOG\Sanitize;

class CRUDTableWidgetWeight implements InterfaceCRUDTableWidget
{
    const FORMFIELD_CONTEXT_FIELDS_NAME = 'context_field_names';

    protected $context_fields_arr = [];
    protected $button_text;
    protected $button_class_str;

    public function __construct($context_fields_arr, $button_text = '', $button_class_str = 'btn btn-xs btn-default glyphicon glyphicon-arrow-up'){
        $this->context_fields_arr = $context_fields_arr;
        $this->button_class_str = $button_class_str;
        $this->button_text = $button_text;
    }

    /**
     * @param $obj InterfaceWeight
     * @return string
     */
    public function html($obj){
        Assert::assert($obj);

        $o = '';
        $o .= '<form style="display: inline;" method="post" action="' . \OLOG\Url::getCurrentUrl() . '">';
        $o .= Operations::operationCodeHiddenField(CRUDTable::OPERATION_SWAP_MODEL_WEIGHT);
        $o .= '<input type="hidden" name="' . self::FORMFIELD_CONTEXT_FIELDS_NAME . '" value="' . Sanitize::sanitizeAttrValue(implode(',', array_keys($this->context_fields_arr))) . '">';

        foreach ($this->context_fields_arr as $context_field_name => $context_field_value) {
            $context_field_value = CRUDCompiler::compile($context_field_value, ['this' => $obj]);
            if ($context_field_value == 'NULL'){
                $context_field_value = null;
            }
            $o .= NullablePostFields::hiddenFieldHtml($context_field_name, $context_field_value);
        }

        $o .= '<input type="hidden" name="_class_name" value="' . Sanitize::sanitizeAttrValue(get_class($obj)) . '">';
        $o .= '<input type="hidden" name="_id" value="' . Sanitize::sanitizeAttrValue(CRUDFieldsAccess::getObjId($obj)) . '">';

        $o .= '<button class="' . $this->button_class_str . '" type="submit">' . $this->button_text .'</button>';

        $o .= '</form>';

        return $o;
    }
}