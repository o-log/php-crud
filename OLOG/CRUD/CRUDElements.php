<?php

namespace OLOG\CRUD;

class CRUDElements
{
    const ELEMENT_FORM_ROW = 'ELEMENT_FORM_ROW';
    const ELEMENT_VERTICAL_FORM_ROW = 'ELEMENT_VERTICAL_FORM_ROW';
    const OPERATION_SAVE_EDITOR_FORM = 'OPERATION_SAVE_EDITOR_FORM';
    const KEY_ELEMENT_TYPE = 'ELEMENT_TYPE';
    const KEY_FORM_ROW_FIELD_NAME = 'FORM_ROW_FIELD_NAME';
    const KEY_FORM_ROW_TITLE = 'FORM_ROW_TITLE';

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
        $element_type = $config_arr[self::KEY_ELEMENT_TYPE];

        switch ($element_type){
            case self::ELEMENT_FORM_ROW:
                self::renderFormRow($config_arr, $obj);
                break;

            case self::ELEMENT_VERTICAL_FORM_ROW:
                self::renderVerticalFormRow($config_arr, $obj);
                break;

            default:
                throw new \Exception('unknown element type');
        }

    }

    static protected function saveEditorFormOperation($model_class_name, $object_id){
        \OLOG\Model\Helper::exceptionIfClassNotImplementsInterface($model_class_name, \OLOG\Model\InterfaceSave::class);

        $new_prop_values_arr = array();
        $reflect = new \ReflectionClass($model_class_name);

        foreach ($reflect->getProperties() as $prop_obj) {
            if (!$prop_obj->isStatic()) { // игнорируем статические свойства класса - они относятся не к объекту, а только к классу (http://www.php.net/manual/en/language.oop5.static.php), и в них хранятся настройки ActiveRecord и CRUD
                $prop_name = $prop_obj->getName();
                if (array_key_exists($prop_name, $_POST)) {
                    // Проверка на заполнение обязательных полей делается на уровне СУБД, через нот нулл в таблице
                    $new_prop_values_arr[$prop_name] = $_POST[$prop_name];
                }
            }
        }

        //
        // сохранение
        //

        $obj = ObjectLoader::createAndLoadObject($model_class_name, $object_id);
        $obj = FieldsAccess::setObjectFieldsFromArray($obj, $new_prop_values_arr);
        $obj->save();

        /* TODO: внести логирование в save?
        \Sportbox\Logger\Logger::logObjectEvent($obj, 'CRUD сохранение');
        $redirect_url = \Sportbox\CRUD\ControllerCRUD::getEditUrlForObj($obj);
        */

        // keep get form
        \OLOG\Redirects::redirectToSelf();
    }

    static public function renderEditorForm($class_name, $obj_id, $config_arr){
        Operations::matchOperation(self::OPERATION_SAVE_EDITOR_FORM, function() use($class_name, $obj_id, $config_arr) {
            self::saveEditorFormOperation($class_name, $obj_id);
        });

        echo '<form id="form" class="form-horizontal" role="form" method="post" action="' . Sanitize::sanitizeUrl(\OLOG\Url::getCurrentUrl()) . '">';

        echo Operations::operationCodeHiddenField(self::OPERATION_SAVE_EDITOR_FORM);
        echo '<input type="hidden" name="_class_name" value="' . Sanitize::sanitizeAttrValue($class_name) . '">';
        echo '<input type="hidden" name="_obj_id" value="' . Sanitize::sanitizeAttrValue($obj_id) . '">';

        $obj = ObjectLoader::createAndLoadObject($class_name, $obj_id);

        $elements_arr = CRUDConfigReader::getRequiredSubkey($config_arr, 'ELEMENTS');
        foreach ($elements_arr as $element_key => $element_config){
            self::renderFormElement($element_config, $obj);
        }

        echo '<div class="row">';
        echo '<div class="col-sm-8 col-sm-offset-4">';
        echo '<button style="width: 100%" type="submit" class="btn btn-primary">Сохранить</button>';
        echo '</div>';
        echo '</div>';

        echo '</form>';
    }

    static public function renderFormRow($element_config_arr, $obj){
        $required = false;
        // TODO
        //$required = \Sportbox\CRUD\Helpers::isRequiredField($model_class_name, $prop_obj->getName());

        $editor_description = '';
        // TODO
        //$editor_description = \Sportbox\CRUD\Helpers::getDescriptionForField($model_class_name, $prop_obj->getName());

        //$editor_context_obj = CRUDController::getEditorContext();

        //$field_name = $element_config_arr[self::KEY_FORM_ROW_FIELD_NAME];
        $field_name = CRUDConfigReader::getRequiredSubkey($element_config_arr, self::KEY_FORM_ROW_FIELD_NAME);
        $field_title = CRUDConfigReader::getOptionalSubkey($element_config_arr, self::KEY_FORM_ROW_TITLE, $field_name);

        echo '<div class="form-group ' . ($required ? 'required' : '') . '">';
        echo '<label class="col-sm-4 text-right control-label" for="' . $field_name . '">' . $field_title . '</label>';

        echo '<div class="col-sm-8">';
        $widget_config_arr = CRUDConfigReader::getRequiredSubkey($element_config_arr, 'WIDGET');
        echo \OLOG\CRUD\CRUDWidgets::renderEditorFieldWithWidget($widget_config_arr, $field_name, $obj);

        if ($editor_description) {
            echo '<span class="help-block">' . $editor_description . '</span>';
        }

        echo '</div>';
        echo '</div>';
    }

    static public function renderVerticalFormRow($element_config_arr, $obj){
        $field_name = CRUDConfigReader::getRequiredSubkey($element_config_arr, self::KEY_FORM_ROW_FIELD_NAME);
        $field_title = CRUDConfigReader::getOptionalSubkey($element_config_arr, self::KEY_FORM_ROW_TITLE, $field_name);

        echo '<div class="form-group">';
        echo '<div class="col-sm-12" style="font-weight: bold;">' . $field_title . '</div>';

        echo '<div class="col-sm-12">';
        $widget_config_arr = CRUDConfigReader::getRequiredSubkey($element_config_arr, 'WIDGET');
        echo \OLOG\CRUD\CRUDWidgets::renderEditorFieldWithWidget($widget_config_arr, $field_name, $obj);

        echo '</div>';
        echo '</div>';
    }
}