<?php

namespace OLOG\CRUD;

class CRUDEditorForm
{
    const OPERATION_SAVE_EDITOR_FORM = 'OPERATION_SAVE_EDITOR_FORM';

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

    static public function getHtml($class_name, $obj_id, $elements_arr){
        $html = '';
        
        Operations::matchOperation(self::OPERATION_SAVE_EDITOR_FORM, function() use($class_name, $obj_id) {
            self::saveEditorFormOperation($class_name, $obj_id);
        });

        $html .= '<form id="form" class="form-horizontal" role="form" method="post" action="' . Sanitize::sanitizeUrl(\OLOG\Url::getCurrentUrl()) . '">';

        $html .= Operations::operationCodeHiddenField(self::OPERATION_SAVE_EDITOR_FORM);
        $html .= '<input type="hidden" name="_class_name" value="' . Sanitize::sanitizeAttrValue($class_name) . '">';
        $html .= '<input type="hidden" name="_obj_id" value="' . Sanitize::sanitizeAttrValue($obj_id) . '">';

        $obj = ObjectLoader::createAndLoadObject($class_name, $obj_id);

        //$elements_arr = CRUDConfigReader::getRequiredSubkey($config_arr, 'ELEMENTS');
        foreach ($elements_arr as $element_html){
            //self::renderFormElement($element_config, $obj);
            $html .= $element_html;
        }

        $html .= '<div class="row">';
        $html .= '<div class="col-sm-8 col-sm-offset-4">';
        $html .= '<button style="width: 100%" type="submit" class="btn btn-primary">Сохранить</button>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '</form>';
        
        return $html;
    }
}