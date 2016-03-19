<?php

namespace OLOG\CRUD;

// TODO: remove whole class?

class CRUDElements
{
    const ELEMENT_FORM_ROW = 'ELEMENT_FORM_ROW';
    const ELEMENT_VERTICAL_FORM_ROW = 'ELEMENT_VERTICAL_FORM_ROW';
    const KEY_ELEMENT_CLASS = 'KEY_ELEMENT_CLASS';

    // TODO: remove
    const KEY_ELEMENT_TYPE = 'ELEMENT_TYPE';

    /*
    static public function renderElements($elements_arr){
        foreach ($elements_arr as $element_key => $element_config_arr){
            CRUDElements::renderElement($element_config_arr);
        }
    }
    */

    /**
     * Элемент формы рисуется для объекта (а не для пары класс-идентификатор), чтобы ему можно было передать или редактируемый объект для формы редактирования, или объект со значениями полей по умолчанию для формы создания.
     * @param $config_arr
     * @param $obj
     * @throws \Exception
     */
    static public function renderFormElement($config_arr, $obj){
        //$element_type = $config_arr[self::KEY_ELEMENT_TYPE];
        $element_class = CRUDConfigReader::getRequiredSubkey($config_arr, self::KEY_ELEMENT_CLASS);

        // TODO: check element class interfaces
        $element_class::render($config_arr, $obj);

        /*
        switch ($element_type){
            //case self::ELEMENT_FORM_ROW:
                //self::renderFormRow($config_arr, $obj);
                //break;

            case self::ELEMENT_VERTICAL_FORM_ROW:
                self::renderVerticalFormRow($config_arr, $obj);
                break;

            default:
                throw new \Exception('unknown element type');
        }
        */

    }

}