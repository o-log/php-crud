<?php

namespace OLOG\CRUD;

class FGroup implements FGroupInterface
{
    protected $title;
    protected $widget_obj;
    protected $comment_str;

    /**
     * @return mixed
     */
    public function getCommentStr()
    {
        return $this->comment_str;
    }

    /**
     * @param mixed $comment_str
     */
    public function setCommentStr($comment_str)
    {
        $this->comment_str = $comment_str;
    }

    public function __construct($title, FWInterface $widget_obj, $comment_str = '')
    {
        $this->setTitle($title);
        $this->setWidgetObj($widget_obj);
        $this->setCommentStr($comment_str);
    }

    public function html($obj){
        $html = '';

        $html .= '<div class="form-group">';
        $html .= '<div class=""><label>' . $this->getTitle() . '</label></div>';

        $html .= '<div class="">';

        $widget_obj = $this->getWidgetObj();

        // TODO: check widget interface

        $html .= $widget_obj->html($obj);

        if ($this->getCommentStr()) {
            $html .= '<div class="col-sm-12">';
            $html .= '<small class="form-text text-muted">' . $this->getCommentStr() . '</small>';
            $html .= '</div>';
        }

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