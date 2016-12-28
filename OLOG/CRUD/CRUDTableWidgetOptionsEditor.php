<?php

namespace OLOG\CRUD;

use OLOG\HTML;
use OLOG\Operations;

class CRUDTableWidgetOptionsEditor implements InterfaceCRUDTableWidget
{
    protected $value;
    protected $options_arr;
    protected $crudtable_id;

    /**
     * Returns sanitized content.
     * @param $obj
     * @return string
     */
    public function html($obj)
    {
	    return HTML::tag('form', ['class' => 'js-options-editor'], function () use ($obj) {
		    $options_arr = $this->getOptionsArr();
		    foreach ($options_arr as $value => $option_name) {
			    $disabled = '';
		    	if ($value == $obj->getVocabularyId()) {
				    $disabled = 'style="opacity:0.5;" disabled';
			    }
		    	echo '<button class="btn btn-xs btn-default" type="submit" name="' . CRUDTable::FIELD_FIELD_VALUE . '" value="' . $value . '" ' . $disabled . '>' . $option_name . '</button>';
		    }

		    echo '<input type="hidden" name="' . Operations::FIELD_NAME_OPERATION_CODE . '" value="' . CRUDTable::OPERATION_UPDATE_MODEL_FIELD . '">';
		    echo '<input type="hidden" name="' . CRUDTable::FIELD_FIELD_NAME . '" value="' . $this->getValue() . '">';
		    echo '<input type="hidden" name="' . CRUDTable::FIELD_CRUDTABLE_ID . '" value="' . $this->getCrudtableId() . '">';
		    echo '<input type="hidden" name="' . CRUDTable::FIELD_MODEL_ID . '" value="' . $obj->getId() . '">';
		    echo '<input type="hidden" name="' . CRUDTable::FIELD_FIELD_VALUE . '" value="' . $obj->getVocabularyId() . '">';
	    });
    }

    public function __construct($value, $options_arr, $crudtable_id)
    {
	    $this->setValue($value);
        $this->setOptionsArr($options_arr);
        $this->setCrudtableId($crudtable_id);
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
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