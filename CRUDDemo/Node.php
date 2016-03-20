<?php

namespace CRUDDemo;

class Node implements
    \OLOG\Model\InterfaceFactory,
    \OLOG\Model\InterfaceLoad,
    \OLOG\Model\InterfaceSave
{
    use \OLOG\Model\FactoryTrait;
    use \OLOG\Model\ActiveRecord;
    use \OLOG\Model\ProtectProperties;

    const DB_ID = \CRUDDemo\Config::DB_NAME_PHPCRUDDEMO;
    const DB_TABLE_NAME = 'node';

    protected $id;
    protected $state_code = 0;
    protected $body = 0;
    protected $title = '';

    /**
     * @return int
     */
    public function getStateCode()
    {
        return $this->state_code;
    }

    /**
     * @param int $state_code
     */
    public function setStateCode($state_code)
    {
        $this->state_code = $state_code;
    }

    /**
     * @return int
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param int $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    
    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }
}