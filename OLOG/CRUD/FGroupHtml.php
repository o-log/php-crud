<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG\CRUD;

class FGroupHtml implements FGroupInterface
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
