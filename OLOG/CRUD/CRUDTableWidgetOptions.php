<?php

namespace OLOG\CRUD;

use OLOG\Sanitize;

class CRUDTableWidgetOptions implements InterfaceCRUDTableWidget
{
    protected $value;
    protected $options_arr;

    /**
     * Returns sanitized content.
     * @param $obj
     * @return string
     */
    public function html($obj)
    {
        $value = CRUDCompiler::compile($this->getValue(), ['this' => $obj]);

        $html = "UNDEFINED";
        $options_arr = $this->getOptionsArr();
        if (array_key_exists($value, $options_arr)) {
            $html = $options_arr[$value];
        }
        return Sanitize::sanitizeTagContent($html);
    }

    public function __construct($value, $options_arr)
    {
        $this->setOptionsArr($options_arr);
        $this->setValue($value);
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return array
     */
    public function getOptionsArr()
    {
        return $this->options_arr;
    }

    /**
     * @param array $options_arr
     */
    public function setOptionsArr($options_arr)
    {
        $this->options_arr = $options_arr;
    }

}