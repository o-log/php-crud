<?php

namespace OLOG\CRUD;

class CRUDFormRowHtml implements InterfaceCRUDFormRow
{
    protected $html;

    public function __construct($html)
    {
        $this->setHtml($html);
    }

    public function html($obj){
        $html = '';

        $html .= $this->getHtml();

        return $html;
    }

    /**
     * @return mixed
     */
    public function getHtml()
    {
        return $this->html;
    }

    /**
     * @param mixed $html
     */
    public function setHtml($html)
    {
        $this->html = $html;
    }
}