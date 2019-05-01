<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG\CRUD;

class TWHtml implements TWInterface
{
    protected $html;

    public function __construct($html)
    {
        $this->setHtml($html);
    }

    public function html($obj){
        return  CCompiler::fieldValueOrCallableResult($this->getHtml(), $obj);
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
