<?php

namespace OLOG\CRUD;

class CRUDElementVerticalFormRow
{
    protected $title;
    protected $widget_obj;

    public function __construct($title, $widget_obj)
    {
        $this->setTitle($title);
        $this->setWidgetObj($widget_obj);
    }

    public function html($obj){
        //$field_name = CRUDConfigReader::getRequiredSubkey($element_config_arr, self::KEY_FORM_ROW_FIELD_NAME);
        //$field_title = CRUDConfigReader::getOptionalSubkey($element_config_arr, self::KEY_FORM_ROW_TITLE, $field_name);

        $html = '';

        $html .= '<div class="form-group">';
        $html .= '<div class="col-sm-12" style="font-weight: bold;">' . $this->getTitle() . '</div>';

        $html .= '<div class="col-sm-12">';
        //$widget_config_arr = CRUDConfigReader::getRequiredSubkey($element_config_arr, 'WIDGET');

        $widget_obj = $this->getWidgetObj();

        // TODO: check widget interface

        $html .= $widget_obj->html($obj);

        $html .= '</div>';
        $html .= '</div>';

        return $html;
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