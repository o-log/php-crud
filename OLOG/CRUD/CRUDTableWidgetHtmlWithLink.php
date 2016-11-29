<?php

namespace OLOG\CRUD;


use OLOG\HTML;

class CRUDTableWidgetHtmlWithLink implements InterfaceCRUDTableWidget
{
    protected $html;
    protected $link;
    protected $classes_str;
    
    public function html($obj){
        $url = CRUDCompiler::compile($this->getLink(), ['this' => $obj]);

	    $html = CRUDCompiler::compile($this->getHtml(), ['this' => $obj]);

        if (trim($html) == ''){
	        $html = '#EMPTY#';
        }

	    return HTML::tag('a', ['href' => $url, 'class' => $this->getClassesStr()], $html);
    }
    
    public function __construct($html, $link, $classes_str = '')
    {
        $this->setHtml($html);
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