<?php

namespace OLOG\CRUD;

use OLOG\Assert;
use OLOG\Operations;
use OLOG\POSTAccess;
use OLOG\Render;
use OLOG\Sanitize;

class CRUDForm
{
    const OPERATION_SAVE_EDITOR_FORM = 'OPERATION_SAVE_EDITOR_FORM';

    const FIELD_CLASS_NAME = '_FIELD_CLASS_NAME';
    const FIELD_OBJECT_ID = '_FIELD_OBJECT_ID';

    /**
     * @param string $url_to_redirect_after_save
     * @param array $redirect_get_params_arr
     * @throws \Exception
     */
    static protected function saveEditorFormOperation($url_to_redirect_after_save = '', $redirect_get_params_arr = [])
    {
        $model_class_name = POSTAccess::getRequiredPostValue(self::FIELD_CLASS_NAME);
        $object_id = POSTAccess::getOptionalPostValue(self::FIELD_OBJECT_ID);

        \OLOG\CheckClassInterfaces::exceptionIfClassNotImplementsInterface($model_class_name, \OLOG\Model\InterfaceSave::class);

        $new_prop_values_arr = [];
        $null_fields_arr = [];
        $reflect = new \ReflectionClass($model_class_name);

        foreach ($reflect->getProperties() as $prop_obj) {
            if (!$prop_obj->isStatic()) { // игнорируем статические свойства класса - они относятся не к объекту, а только к классу (http://www.php.net/manual/en/language.oop5.static.php), и в них хранятся настройки ActiveRecord и CRUD
                $prop_name = $prop_obj->getName();

                // сейчас если поля нет в форме - оно не будет изменено в объекте. это позволяет показывать в форме только часть полей, на остальные форма не повлияет
                if (array_key_exists($prop_name, $_POST)) {
                    // Проверка на заполнение обязательных полей делается на уровне СУБД, через нот нулл в таблице
                    $new_prop_values_arr[$prop_name] = $_POST[$prop_name];
                }

                // чтение возможных NULL
                if (array_key_exists($prop_name . "___is_null", $_POST)) {
                    if ($_POST[$prop_name . "___is_null"]) {
                        $null_fields_arr[$prop_name] = 1;
                    }
                }
            }
        }

        //
        // сохранение или создание
        //

        $obj = null;
        if ($object_id) {
            $obj = CRUDObjectLoader::createAndLoadObject($model_class_name, $object_id);
        } else {
            $obj = new $model_class_name;
        }

        $obj = CRUDFieldsAccess::setObjectFieldsFromArray($obj, $new_prop_values_arr, $null_fields_arr);
        $obj->save();

        /* TODO: внести логирование в save?
        \OLOG\Logger\Logger::logObjectEvent($obj, 'CRUD сохранение');
        */

        if ($url_to_redirect_after_save != '') {
            $redirect_url = $url_to_redirect_after_save;

            $params_arr = [];
            foreach ($redirect_get_params_arr as $param => $value) {
                $params_arr[$param] = CRUDCompiler::compile($value, ['this' => $obj]);
            }

            if (!empty($redirect_get_params_arr)) {
                $redirect_url = $url_to_redirect_after_save . '?' . http_build_query($params_arr);
            }
            \OLOG\Redirects::redirect($redirect_url);
        }

        // keep get form
        \OLOG\Redirects::redirectToSelf();
    }

    /**
     * ид объекта может быть пустым - тогда при сохранении формы создаст новый объект
     * @param $obj
     * @param $element_obj_arr
     * @param string $url_to_redirect_after_save
     * @param array $redirect_get_params_arr
     * @return string html-код формы редактирования
     * @throws \Exception
     */
    static public function html($obj, $element_obj_arr, $url_to_redirect_after_save = '', $redirect_get_params_arr = [])
    {
        static $CRUDForm_include_script;

        $script = '';
        if(!isset($CRUDForm_include_script)){
            $script .= '<script>';
            $script .= Render::callLocaltemplate('templates/crudform.js');
            $script .= '</script>';
            $script .= '<style>.required-class {border: 1px solid red;}.required-class[type="radio"]:before {font-size: 1em;content: \'*\';color: red;}</style>';
            $CRUDForm_include_script = false;
        }

        // TODO: transactions??

        Operations::matchOperation(self::OPERATION_SAVE_EDITOR_FORM, function () use ($url_to_redirect_after_save, $redirect_get_params_arr) {
            self::saveEditorFormOperation($url_to_redirect_after_save, $redirect_get_params_arr);
        });

        $form_element_id = 'formElem_' . uniqid();

        $html = '';

        $html .= '<form id="' . $form_element_id . '" class="form-horizontal" role="form" method="post" action="' . Sanitize::sanitizeUrl(\OLOG\Url::getCurrentUrl()) . '">';

        $html .= Operations::operationCodeHiddenField(self::OPERATION_SAVE_EDITOR_FORM);

        $html .= '<input type="hidden" name="' . self::FIELD_CLASS_NAME . '" value="' . Sanitize::sanitizeAttrValue(get_class($obj)) . '">';
        $html .= '<input type="hidden" name="' . self::FIELD_OBJECT_ID . '" value="' . Sanitize::sanitizeAttrValue(CRUDFieldsAccess::getObjId($obj)) . '">';

        /** @var InterfaceCRUDFormRow $element_obj */
        foreach ($element_obj_arr as $element_obj) {
            Assert::assert($element_obj instanceof InterfaceCRUDFormRow);
            $html .= $element_obj->html($obj);
        }

        $html .= '<div class="row">';
        $html .= '<div class="col-sm-8 col-sm-offset-4">';
        $html .= '<button style="width: 100%" type="submit" class="btn btn-primary">Сохранить</button>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '</form>';
        $html .= '<script>CRUD.Form.init("' . $form_element_id . '");</script>';

        return $script . $html;
    }
}