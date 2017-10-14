<?php

namespace OLOG\CRUD;

use OLOG\HTML;

class CRUDTableWidgetTextWithLink implements InterfaceCRUDTableWidget
{
    protected $text;
    protected $link;
    protected $classes_str;
    
    public function html($obj){
        $url = CRUDCompiler::compile($this->getLink(), ['this' => $obj]);

        $text = CRUDCompiler::compile($this->getText(), ['this' => $obj]);

        if (trim($text) == ''){
            $text = '#EMPTY#';
        }

        $o = HTML::a($url, $text, $this->getClassesStr());

        return $o;
        
    }
    
    public function __construct($text, $link, $classes_str = '')
    {
        $this->setText($text);
        $this->setLink($link);
        $this->setClassesStr($classes_str);
    }

    /**
     * @return mixed
     */
    public function getClassesStr()
    {
        return $this->classes_str;
    }

    /**
     * @param mixed $classes_str
     */
    public function setClassesStr($classes_str)
    {
        $this->classes_str = $classes_str;
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