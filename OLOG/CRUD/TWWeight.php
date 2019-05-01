<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG\CRUD;

use OLOG\Model\WeightInterface;
use OLOG\Form;
use OLOG\HTML;

class TWWeight implements TWInterface
{
    const FORMFIELD_CONTEXT_FIELDS_NAME = 'context_field_names';

    protected $context_fields_arr = [];
    protected $button_text;
    protected $button_class_str;

    public function __construct($context_fields_arr, $button_text = '', $button_class_str = 'btn btn-sm btn-outline-secondary fa fa-arrow-up'){
        $this->context_fields_arr = $context_fields_arr;
        $this->button_class_str = $button_class_str;
        $this->button_text = $button_text;
    }

    /**
     * @param $obj WeightInterface
     * @return string
     */
    public function html($obj){
        assert($obj);

        $o = '';
        $o .= '<form style="display: inline;" method="post" action="' . \OLOG\Url::path() . '">';
        $o .= Form::op(CTable::OPERATION_SWAP_MODEL_WEIGHT);
        $o .= '<input type="hidden" name="' . self::FORMFIELD_CONTEXT_FIELDS_NAME . '" value="' . HTML::attr(implode(',', array_keys($this->context_fields_arr))) . '">';

        foreach ($this->context_fields_arr as $context_field_name => $context_field_value) {
            $context_field_value = CCompiler::fieldValueOrCallableResult($context_field_value, $obj);
            $o .= NullablePostFields::hiddenFieldHtml($context_field_name, $context_field_value);
        }

        $o .= '<input type="hidden" name="_class_name" value="' . HTML::attr(get_class($obj)) . '">';
        $o .= '<input type="hidden" name="_id" value="' . HTML::attr(CInternalFieldsAccess::getObjId($obj)) . '">';

        $o .= '<button class="' . $this->button_class_str . '" type="submit">' . $this->button_text .'</button>';

        $o .= '</form>';

        return $o;
    }
}
