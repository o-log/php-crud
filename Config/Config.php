<?php

namespace Config;

use CRUDDemo\CRUDDemoLayout;
use OLOG\BT\LayoutBootstrap4;
use OLOG\Cache\BucketMemcache;
use OLOG\Cache\CacheConfig;
use OLOG\Cache\MemcacheServer;
use OLOG\DB\ConnectorMySQL;
use OLOG\DB\DBConfig;
use OLOG\DB\Space;
use OLOG\Layouts\LayoutsConfig;

class Config
{
    const CONNECTOR_CRUDDEMO = 'connector_phpcrud';
    const SPACE_CRUDDEMO = 'phpcrud';
    const PERMISSION_EDIT_NODES = 'PERMISSION_EDIT_NODES';

    public static function init()
    {
        ini_set('assert.exception', true);
        date_default_timezone_set('Europe/Moscow');

        //$conf['return_false_if_no_route'] = true; // for local php server

        DBConfig::setConnector(
            self::CONNECTOR_CRUDDEMO,
            new ConnectorMySQL('127.0.0.1', 'phpcrud', 'root', '1')
        );

        DBConfig::setSpace(
            self::SPACE_CRUDDEMO,
            new Space(self::CONNECTOR_CRUDDEMO, 'phpcrud.sql')
        );

        CacheConfig::setBucket('', new BucketMemcache([new MemcacheServer('localhost', 11211)]));
    }
}
