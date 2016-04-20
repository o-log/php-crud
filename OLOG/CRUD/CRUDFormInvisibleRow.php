<?php

namespace OLOG\CRUD;

class CRUDFormInvisibleRow implements InterfaceCRUDFormRow
{
    protected $widget_obj;

    public function __construct(InterfaceCRUDFormWidget $widget_obj)
    {
        $this->setWidgetObj($widget_obj);
    }

    public function html($obj){
        $html = '';

        $required = false;
        // TODO
        //$required = \Sportbox\CRUD\Helpers::isRequiredField($model_class_name, $prop_obj->getName());

        //$editor_context_obj = CRUDController::getEditorContext();

        $html .= '<div style="display: none;">';
        $widget_obj = $this->getWidgetObj();

        // TODO: check widget interface

        $html .= $widget_obj->html($obj);

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
}