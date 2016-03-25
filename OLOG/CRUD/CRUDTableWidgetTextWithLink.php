<?php

namespace OLOG\CRUD;

use OLOG\Sanitize;

class CRUDTableWidgetTextWithLink implements InterfaceCRUDTableWidget
{
    protected $text;
    protected $link;
    
    public function html($obj){
        $url = CRUDCompiler::compile($this->getLink(), ['this' => $obj]);

        $text = CRUDCompiler::compile($this->getText(), ['this' => $obj]);

        if (trim($text) == ''){
            $text = '#EMPTY#';
        }

        $o = '<a href="' . Sanitize::sanitizeUrl($url) . '">' . Sanitize::sanitizeTagContent($text) . '</a>';

        return $o;
        
    }
    
    public function __construct($text, $link)
    {
        $this->setText($text);
        $this->setLink($link);
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

    /**
     * @return mixed
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param mixed $link
     */
    public function setLink($link)
    {
        $this->link = $link;
    }
    
    
}