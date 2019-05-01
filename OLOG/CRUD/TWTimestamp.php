<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG\CRUD;

use OLOG\HTML;

class TWTimestamp implements TWInterface
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
        $timestamp = CCompiler::fieldValueOrCallableResult($this->getTimestamp(), $obj);
        if (is_null($timestamp)){
            return '';
        }

        if ($this->getFormat() == ''){
            if (date('Ymd') == date('Ymd', $timestamp)){
                $date = date("H:i", $timestamp);
            } else {
                $date = date("d.m.Y", $timestamp);
            }
        } else {
            $date = date($this->getFormat(), $timestamp);
        }
        return HTML::content($date);
    }

    public function __construct($timestamp, $format = '')
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
