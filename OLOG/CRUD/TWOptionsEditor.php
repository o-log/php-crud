<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG\CRUD;

use OLOG\HTML;
use OLOG\Form;

class TWOptionsEditor implements TWInterface
{
    protected $field_name;
    protected $options_arr;
    protected $crudtable_id;

    /**
     * Returns sanitized content.
     * @param $obj
     * @return string
     */
    public function html($obj)
    {
        return HTML::tag('form', ['class' => 'js-options-editor btn-group'], function () use ($obj) {
            echo '<input type="hidden" name="' . Form::FIELD_NAME_OPERATION_CODE . '" value="' . CTable::OPERATION_UPDATE_MODEL_FIELD . '">';
            echo '<input type="hidden" name="' . CTable::FIELD_FIELD_NAME . '" value="' . $this->getFieldName() . '">';
            echo '<input type="hidden" name="' . CTable::FIELD_CRUDTABLE_ID . '" value="' . $this->getCrudtableId() . '">';
            echo '<input type="hidden" name="' . CTable::FIELD_MODEL_ID . '" value="' . $obj->getId() . '">';
            echo '<input type="hidden" name="' . CTable::FIELD_FIELD_VALUE . '" value="' .  CInternalFieldsAccess::getObjectFieldValue($obj, $this->getFieldName()) . '">';

            $options_arr = $this->getOptionsArr();
            $obj_value = CInternalFieldsAccess::getObjectFieldValue($obj, $this->getFieldName());
            foreach ($options_arr as $value => $option_name) {
                $disabled = '';
                if ($value == $obj_value) {
                    $disabled = 'style="opacity:0.5;" disabled';
                }
                echo '<button class="btn btn-sm btn-outline-secondary" type="submit" name="' . CTable::FIELD_FIELD_VALUE . '" value="' . $value . '" ' . $disabled . '>' . $option_name . '</button>';
            }
        });
    }

    public function __construct($field_name, $options_arr, $crudtable_id)
    {
        $this->setFieldName($field_name);
        $this->setOptionsArr($options_arr);
        $this->setCrudtableId($crudtable_id);
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


    /**
     * @return array
     */
    public function getOptionsArr()
    {
        return $this->options_arr;
    }

    /**
     * @param array $options_arr
     */
    public function setOptionsArr($options_arr)
    {
        $this->options_arr = $options_arr;
    }

    /**
     * @return mixed
     */
    public function getCrudtableId()
    {
        return $this->crudtable_id;
    }

    /**
     * @param mixed $crudtable_id
     */
    public function setCrudtableId($crudtable_id)
    {
        $this->crudtable_id = $crudtable_id;
    }

}
