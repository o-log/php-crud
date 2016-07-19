<?php

namespace CRUDDemo;

use OLOG\DB\DBConfig;
use OLOG\DB\DBSettings;
use OLOG\Model\ModelConstants;

class CrudDemoConfig
{
    const DB_NAME_PHPCRUDDEMO = 'phpcrud';
    const PERMISSION_EDIT_NODES = 'PERMISSION_EDIT_NODES';

    public static function init()
    {
        date_default_timezone_set('Europe/Moscow');

        //$conf['return_false_if_no_route'] = true; // for local php server

        DBConfig::setDBSettingsObj(
            self::DB_NAME_PHPCRUDDEMO,
            new DBSettings('localhost', 'phpcrud', 'root', '1')
        );

        /*
        $conf[ModelConstants::MODULE_CONFIG_ROOT_KEY]['db'] = [
            self::DB_NAME_PHPCRUDDEMO => [
                'host' => 'localhost',
                'db_name' => 'phpcrud',
                'user' => 'root',
                'pass' => '1'
            ]
        ];

        return $conf;
        */
    }
}