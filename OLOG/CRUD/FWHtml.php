<?php

namespace OLOG\CRUD;

class FWHtml implements FWInterface
{
    protected $html = '';

    /**
     * @return string
     */
    public function getHtml()
    {
        return $this->html;
    }

    /**
     * @param string $html
     */
    public function setHtml($html)
    {
        $this->html = $html;
    }


    public function __construct($html)
    {
        $this->setHtml($html);
    }

    public function html($obj)
    {
        return $this->getHtml();
    }
}