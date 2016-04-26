<?php

namespace OLOG\CRUD;


class CRUDTableWidgetProgressBar implements InterfaceCRUDTableWidget
{
    protected $progress;

    /**
     * Returns sanitized content.
     * @param $obj
     * @return mixed
     */
    public function html($obj)
    {
        $progress_value = CRUDCompiler::compile($this->getProgress(), ['this' => $obj]);

        return \OLOG\BT::progress($progress_value);
    }

    public function __construct($progress)
    {
        $this->setProgress($progress);
    }

    /**
     * @return int
     */
    public function getProgress()
    {
        return $this->progress;
    }

    /**
     * @param int $progress
     */
    public function setProgress($progress)
    {
        $this->progress = $progress;
    }
}