<?php

namespace OLOG\CRUD;

use OLOG\HTML;

class CRUDTableWidgetDatetime implements InterfaceCRUDTableWidget
{
	protected $datetime;
	protected $format;

	/**
	 * Returns sanitized content.
	 * @param $obj
	 * @return mixed
	 */
	public function html($obj)
	{
		$datetime = CRUDCompiler::compile($this->getDatetime(), ['this' => $obj]);
		$date_obj = new \DateTime($datetime);
		$date = $date_obj->format($this->getFormat());
		return HTML::content($date);
	}

	public function __construct($datetame, $format = "d.m.Y H:i:s")
	{
		$this->setDatetime($datetame);
		$this->setFormat($format);
	}

	/**
	 * @return int
	 */
	public function getDatetime()
	{
		return $this->datetime;
	}

	/**
	 * @param int $timestamp
	 */
	public function setDatetime($datetame)
	{
		$this->datetime = $datetame;
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