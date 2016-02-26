<?php

namespace OLOG\CRUD;

class Elements
{
    const ELEMENT_FORM = 'ELEMENT_FORM';
    const ELEMENT_FORM_ROW = 'ELEMENT_FORM_ROW';

    static public function renderElement($config_arr){
        $element_type = $config_arr['TYPE'];

        switch ($element_type){
            case self::ELEMENT_FORM:
                return self::renderElementForm($config_arr);
                break;

            case self::ELEMENT_FORM_ROW:
                return self::renderElementFormRow($config_arr);
                break;

            default:
                throw new \Exception('unknown element type');
        }

    }

    static public function renderElementForm($config_arr){
        $editor_context_obj = CRUDController::getEditorContext();

        $form_action_url = CRUDController::editAction(\OLOG\Router::GET_URL, $editor_context_obj->bubble_key, $editor_context_obj->object_id, $editor_context_obj->tab_key);
        echo '<form id="form" class="form-horizontal" role="form" method="post" action="' . $form_action_url . '">';

        echo Operations::operationCodeHiddenField(CRUDController::OPERATION_SAVE_EDITOR_FORM);

        foreach ($config_arr['ELEMENTS'] as $element_key => $element_config){
            echo self::renderElement($element_config);
        }

        echo '<div class="row">';
        echo '<div class="col-sm-8 col-sm-offset-4">';
        echo '<button style="width: 100%" type="submit" class="btn btn-primary">Сохранить</button>';
        echo '</div>';
        echo '</div>';

        echo '</form>';
    }

    static public function renderElementFormRow($element_config_arr){
        $required = false;
        // TODO
        //$required = \Sportbox\CRUD\Helpers::isRequiredField($model_class_name, $prop_obj->getName());

        $editor_description = '';
        // TODO
        //$editor_description = \Sportbox\CRUD\Helpers::getDescriptionForField($model_class_name, $prop_obj->getName());

        $editor_context_obj = CRUDController::getEditorContext();

        $model_class_name = CRUDConfigReader::getModelClassNameForKey($editor_context_obj->bubble_key);
        $obj = ObjectLoader::createAndLoadObject($model_class_name, $editor_context_obj->object_id);

        $field_name = $element_config_arr['FIELD_NAME'];

        // TODO: read title from config
        $field_title = $field_name;

        $value = FieldsAccess::getObjectFieldValue($obj, $field_name);

        echo '<div class="form-group ' . ($required ? 'required' : '') . '">';
        echo '<label class="col-sm-4 text-right control-label" for="' . $field_name . '">' . $field_title . '</label>';

        echo '<div class="col-sm-8">';
        echo \OLOG\CRUD\Widgets::renderEditorFieldWithWidget($field_name, $obj);

        if ($editor_description) {
            echo '<span class="help-block">' . $editor_description . '</span>';
        }

        echo '</div>';
        echo '</div>';
    }
}