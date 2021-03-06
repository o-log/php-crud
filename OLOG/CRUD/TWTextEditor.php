<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG\CRUD;

use OLOG\HTML;
use OLOG\Form;

class TWTextEditor implements TWInterface
{
    protected $field_name;
    protected $text;
    protected $crudtable_id;

    /**
     * Returns sanitized content.
     * @param $obj
     * @return mixed
     */
    public function html($obj){
        return HTML::tag('form', ['class' => 'js-text-editor form-inline'], function () use ($obj) {
            echo '<input type="hidden" name="' . Form::FIELD_NAME_OPERATION_CODE . '" value="' . CTable::OPERATION_UPDATE_MODEL_FIELD . '">';
            echo '<input type="hidden" name="' . CTable::FIELD_FIELD_NAME . '" value="' . $this->getFieldName() . '">';
            echo '<input type="hidden" name="' . CTable::FIELD_CRUDTABLE_ID . '" value="' . $this->getCrudtableId() . '">';
            echo '<input type="hidden" name="' . CTable::FIELD_MODEL_ID . '" value="' . $obj->getId() . '">';
            echo '<input type="text" class="form-control form-control-sm" name="' . CTable::FIELD_FIELD_VALUE . '" value="' .  CInternalFieldsAccess::getObjectFieldValue($obj, $this->getFieldName()) . '">';
            echo '<button class="btn btn-sm btn-outline-secondary fa fa-check" type="submit"></button>';
        });
    }

    public function __construct($field_name, $text, $crudtable_id)
    {
        $this->setFieldName($field_name);
        $this->setText($text);
        $this->setCrudtableId($crudtable_id);
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     */
    public function setText($text)
    {
        $this->text = $text;
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
