<?php

namespace OLOG\CRUD;

class TCol implements TColInterface
{
    public $title;
    public $widget_obj;
    
    public function __construct($title, $widget_obj)
    {
        $this->setTitle($title);
        $this->setWidgetObj($widget_obj);
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
        // TODO: check widget object intarfsaceCrudTableWidget
        
        $this->widget_obj = $widget_obj;
    }
    
    
}