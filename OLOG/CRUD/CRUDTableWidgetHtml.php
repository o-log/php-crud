<?php

namespace OLOG\CRUD;


class CRUDTableWidgetHtml implements InterfaceCRUDTableWidget
{
    protected $html;

    public function __construct($html)
    {
        $this->setHtml($html);
    }

    public function html($obj){
        return  CRUDCompiler::compile($this->getHtml(), ['this' => $obj]);
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