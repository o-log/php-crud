<?php

namespace OLOG\CRUD;

class CRUDFormRow implements InterfaceCRUDFormRow
{
    protected $title;
    protected $widget_obj;
    protected $comment_str;

    public function __construct($title, InterfaceCRUDFormWidget $widget_obj, $comment_str = '')
    {
        $this->setTitle($title);
        $this->setWidgetObj($widget_obj);
        $this->setCommentStr($comment_str);
    }

    public function html($obj){
        $html = '';
        
        $required = false;
        // TODO
        
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

        if ($this->getCommentStr()) {
            $html .= '<span class="help-block">' . $this->getCommentStr() . '</span>';
        }

        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }

    public function getCommentStr(){
        return $this->comment_str;
    }

    public function setCommentStr($comment_str){
        $this->comment_str = $comment_str;
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