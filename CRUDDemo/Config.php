<?php

namespace CRUDDemo;

use OLOG\CRUD\CRUDConfigReader;

class Config
{
    const DB_NAME_PHPCRUDDEMO = 'phpcrud';

    public static function get()
    {
        $conf[CRUDConfigReader::CONFIG_ROOT] = [
            'node' => \CRUDDemo\Node::getCRUDConfig()
        ];

        $conf['return_false_if_no_route'] = true; // for local php server

        $conf['db'] = [
            self::DB_NAME_PHPCRUDDEMO => [
                'host' => 'localhost',
                'db_name' => 'phpcrud',
                'user' => 'root',
                'pass' => '1'
            ]
        ];

        return $conf;
    }
}