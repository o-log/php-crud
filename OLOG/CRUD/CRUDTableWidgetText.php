<?php

namespace OLOG\CRUD;

use OLOG\HTML;

class CRUDTableWidgetText implements InterfaceCRUDTableWidget
{
    protected $text;

    /**
     * Returns sanitized content.
     * @param $obj
     * @return mixed
     */
    public function html($obj){
        $html = CRUDCompiler::compile($this->getText(), ['this' => $obj]);
        return HTML::content($html);
    }

    public function __construct($text)
    {
        $this->setText($text);
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }


}