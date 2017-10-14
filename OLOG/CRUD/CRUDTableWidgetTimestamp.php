<?php

namespace OLOG\CRUD;

use OLOG\HTML;

class CRUDTableWidgetTimestamp implements InterfaceCRUDTableWidget
{
    protected $timestamp;
    protected $format;

    /**
     * Returns sanitized content.
     * @param $obj
     * @return mixed
     */
    public function html($obj)
    {
        $timestamp = CRUDCompiler::compile($this->getTimestamp(), ['this' => $obj]);
        $date = date($this->getFormat(), $timestamp);
        return HTML::content($date);
    }

    public function __construct($timestamp, $format = "Y-m-d H:i:s")
    {
        $this->setTimestamp($timestamp);
        $this->setFormat($format);
    }

    /**
     * @return int
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param int $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    public function getFormat()
    {
        return $this->format;
    }

    public function setFormat($format)
    {
        $this->format = $format;
    }
}