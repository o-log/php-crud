<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG\CRUD;

class TCol implements TColInterface
{
    public $title;
    public $widget_obj;
    public $orderby_asc;

    /**
     * @return mixed
     */
    public function getOrderbyAsc()
    {
        return $this->orderby_asc;
    }

    /**
     * @param mixed $order_asc
     */
    public function setOrderbyAsc($orderby_asc): void
    {
        $this->orderby_asc = $orderby_asc;
    }

    public function __construct($title, $widget_obj, $orderby_asc = '')
    {
        $this->setTitle($title);
        $this->setWidgetObj($widget_obj);
        $this->setOrderbyAsc($orderby_asc);
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
