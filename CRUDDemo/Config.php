<?php

namespace CRUDDemo;

class Config
{
    const DB_NAME_PHPCRUDDEMO = 'phpcrud';
    const PERMISSION_EDIT_NODES = 'PERMISSION_EDIT_NODES';

    public static function get()
    {
        date_default_timezone_set('Europe/Moscow');

        $conf['return_false_if_no_route'] = true; // for local php server

        $conf['db'] = [
            self::DB_NAME_PHPCRUDDEMO => [
                'host' => '127.0.0.1',
                'db_name' => 'phpcrud',
                'user' => 'root',
                'pass' => '1'
            ]
        ];

        return $conf;
    }
}