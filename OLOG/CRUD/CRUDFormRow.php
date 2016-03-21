<?php

namespace OLOG\CRUD;

class CRUDFormRow
{
    protected $title;
    protected $widget_obj;

    public function __construct($title, $widget_obj)
    {
        $this->setTitle($title);
        $this->setWidgetObj($widget_obj);
    }

    public function html($obj){
        $html = '';
        
        $required = false;
        // TODO
        //$required = \Sportbox\CRUD\Helpers::isRequiredField($model_class_name, $prop_obj->getName());

        $editor_description = '';
        // TODO
        //$editor_description = \Sportbox\CRUD\Helpers::getDescriptionForField($model_class_name, $prop_obj->getName());

        //$editor_context_obj = CRUDController::getEditorContext();

        //$field_name = $element_config_arr[self::KEY_FORM_ROW_FIELD_NAME];
        //$field_name = CRUDConfigReader::getRequiredSubkey($element_config_arr, self::KEY_FORM_ROW_FIELD_NAME);
        //$field_title = CRUDConfigReader::getOptionalSubkey($element_config_arr, self::KEY_FORM_ROW_TITLE, $field_name);

        $html .= '<div class="form-group ' . ($required ? 'required' : '') . '">';
        $html .= '<label class="col-sm-4 text-right control-label">' . $this->getTitle() . '</label>';

        $html .= '<div class="col-sm-8">';
        //$widget_config_arr = CRUDConfigReader::getRequiredSubkey($element_config_arr, 'WIDGET');
        //echo \OLOG\CRUD\CRUDWidgets::renderEditorFieldWithWidget($widget_config_arr, $field_name, $obj);

        $widget_obj = $this->getWidgetObj();

        // TODO: check widget interface

        $html .= $widget_obj->html($obj);

        if ($editor_description) {
            $html .= '<span class="help-block">' . $editor_description . '</span>';
        }

        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }

    /**
     * @return mixed
     */
    public function getWidgetObj()
    {
        return $this->widget_obj;
    }

    /**
     * @param mixed $widget_obj
     */
    public function setWidgetObj($widget_obj)
    {
        $this->widget_obj = $widget_obj;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }


}