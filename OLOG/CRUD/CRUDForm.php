<?php

namespace OLOG\CRUD;

use OLOG\Form;
use OLOG\Model\ActiveRecordInterface;
use OLOG\POST;
use OLOG\HTML;

class CRUDForm
{
    const OPERATION_SAVE_EDITOR_FORM = 'OPERATION_SAVE_EDITOR_FORM';

    const FIELD_CLASS_NAME = '_FIELD_CLASS_NAME';
    const FIELD_OBJECT_ID = '_FIELD_OBJECT_ID';

    static public function saveOrUpdateObjectFromFormData() {
        $model_class_name = POST::required(self::FIELD_CLASS_NAME);
        $object_id = POST::optional(self::FIELD_OBJECT_ID);

        if (!is_a($model_class_name, ActiveRecordInterface::class, true)){
            throw new \Exception();
        }

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

        $obj = null;
        if ($object_id) {
            $obj = CRUDObjectLoader::createAndLoadObject($model_class_name, $object_id);
        } else {
            $obj = new $model_class_name;
        }

        $obj = CRUDFieldsAccess::setObjectFieldsFromArray($obj, $new_prop_values_arr, $null_fields_arr);
        $obj->save();
        return $obj->getId();
    }

    static protected function saveEditorFormOperation($url_to_redirect_after_save = '', $redirect_get_params_arr = [])
    {
        $model_class_name = POST::required(self::FIELD_CLASS_NAME);
        $object_id = self::saveOrUpdateObjectFromFormData();

        if ($url_to_redirect_after_save != '') {
            $obj = CRUDObjectLoader::createAndLoadObject($model_class_name, $object_id);
            $redirect_url = $url_to_redirect_after_save;
            $redirect_url = CRUDCompiler::compile($redirect_url, ['this' => $obj]);

            $params_arr = [];
            foreach ($redirect_get_params_arr as $param => $value) {
                $params_arr[$param] = CRUDCompiler::compile($value, ['this' => $obj]);
            }

            if (!empty($redirect_get_params_arr)) {
                $redirect_url = $redirect_url . '?' . http_build_query($params_arr);
            }
            \OLOG\Redirects::redirect($redirect_url);
        }

        // keep get form
        \OLOG\Redirects::redirectToSelf();
    }

    static public function executeOperations($url_to_redirect_after_save = '', $redirect_get_params_arr = ''){
        static $__operations_executed = false;

        if ($__operations_executed){
            return;
        }

        $__operations_executed = true;

        Form::match(self::OPERATION_SAVE_EDITOR_FORM, function () use ($url_to_redirect_after_save, $redirect_get_params_arr) {
            self::saveEditorFormOperation($url_to_redirect_after_save, $redirect_get_params_arr);
        });

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
    static public function html($obj, $element_obj_arr, $url_to_redirect_after_save = '', $redirect_get_params_arr = [], $form_id = '', $operation_code = self::OPERATION_SAVE_EDITOR_FORM, $hide_submit_button = false)
    {
        self::executeOperations($url_to_redirect_after_save, $redirect_get_params_arr);

	    $form_element_id = 'formElem_' . uniqid();
	    if ($form_id) {
		    $form_element_id = $form_id;
	    }

        $html = '';

        $html .= '<form id="' . $form_element_id . '" class="form-horizontal" role="form" method="post" action="' . HTML::url(\OLOG\Url::current()) . '">';

        $html .= Form::op($operation_code);

        $html .= '<input type="hidden" name="' . self::FIELD_CLASS_NAME . '" value="' . HTML::attr(get_class($obj)) . '">';
        $html .= '<input type="hidden" name="' . self::FIELD_OBJECT_ID . '" value="' . HTML::attr(CRUDFieldsAccess::getObjId($obj)) . '">';

        /** @var InterfaceCRUDFormRow $element_obj */
        foreach ($element_obj_arr as $element_obj) {
            assert($element_obj instanceof InterfaceCRUDFormRow);
            $html .= $element_obj->html($obj);
        }

        $html .= '<div class="text-right">';
        if (!$hide_submit_button) {
            $html .= '<button type="submit" class="btn btn-primary">Сохранить</button>';
        }
        $html .= '</div>';

        $html .= '</form>';

	    // Загрузка скриптов
	    $html .= CRUDFormScript::getHtml($form_element_id);

        return $html;
    }
}