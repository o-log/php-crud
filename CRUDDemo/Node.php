<?php

/*
 * create table node (id int not null auto_increment primary key, title varchar(250) not null default '') engine InnoDB default charset utf8;
 */

namespace CRUDDemo;

use OLOG\CRUD\CRUDConfigReader;
use OLOG\CRUD\Elements;
use CRUDDemo\Config;

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
    protected $title = '';

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

    static public function getCRUDBubble()
    {
        return [
            CRUDConfigReader::KEY_MODEL_CLASS_NAME => \CRUDDemo\Node::class,
            CRUDConfigReader::KEY_PERMISSIONS_ARR => array(Config::PERMISSION_EDIT_NODES),
            CRUDConfigReader::KEY_LIST_CONFIG => [
                CRUDConfigReader::KEY_ELEMENTS => [
                    [
                        CRUDConfigReader::KEY_ELEMENT_TYPE => Elements::ELEMENT_LIST,
                        CRUDConfigReader::KEY_MODEL_CLASS_NAME => \CRUDDemo\Node::class
                    ]
                ]
            ],
            CRUDConfigReader::KEY_EDITOR_CONFIG => [
                [
                    CRUDConfigReader::KEY_ELEMENTS => [
                        [
                            CRUDConfigReader::KEY_ELEMENT_TYPE => Elements::ELEMENT_FORM,
                            CRUDConfigReader::KEY_ELEMENTS => [
                                [
                                    CRUDConfigReader::KEY_ELEMENT_TYPE => Elements::ELEMENT_FORM_ROW,
                                    'FIELD_NAME' => 'title'
                                ]
                            ]
                        ]
                    ]
                ],
                '_tab_node_terms' => [
                    CRUDConfigReader::KEY_ELEMENTS => [
                        [
                            CRUDConfigReader::KEY_ELEMENT_TYPE => Elements::ELEMENT_LIST
                        ]
                    ]
                ]
            ]
        ];
    }
}