<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG\CRUD;

use OLOG\HTML;

class TWText implements TWInterface
{
    protected $text;

    /**
     * Returns sanitized content.
     * @param $obj
     * @return mixed
     */
    public function html($obj){
        $html = CCompiler::fieldValueOrCallableResult($this->getText(), $obj);
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
