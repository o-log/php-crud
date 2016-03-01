<?php

namespace CRUDDemo;

use OLOG\CRUD\CRUDConfigReader;

class Config
{
    const DB_NAME_PHPCRUDDEMO = 'phpcrud';
    const PERMISSION_EDIT_NODES = 'PERMISSION_EDIT_NODES';

    public static function get()
    {
        $conf[CRUDConfigReader::CONFIG_ROOT] = [
            CRUDConfigReader::CONFIG_KEY_AUTH_PROVIDER_CLASS_NAME => Auth::class,
            'node' => \CRUDDemo\Node::getCRUDBubble()
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